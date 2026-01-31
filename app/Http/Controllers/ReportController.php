<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TreasuryTransaction;
use App\Models\Client;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReportExport;
use App\Exports\GenericExport;

class ReportController extends Controller
{
    // ... (دالة getReportData السابقة كما هي) ...
    private function getReportData(Request $request) { /* ... نفس الكود السابق ... */ 
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $paymentType = $request->input('payment_type', 'all');
        $capital = TreasuryTransaction::where('type', 'income')->where('category', 'رأس المال')->sum('amount');
        
        $revenues = Transaction::with(['nozzle.pump.tank.fuel', 'shift', 'client', 'clientRefuelings'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($paymentType == 'credit', fn($q) => $q->whereNotNull('client_id'))
            ->when($paymentType == 'cash', fn($q) => $q->whereNull('client_id'))
            ->get();
        
        $revenueItems = $revenues->map(function ($transaction) {
            $isCredit = $transaction->clientRefuelings->isNotEmpty();
            $amount = 0;
            
            if ($isCredit) {
                // آجل: ناخد المبلغ من client_refuelings
                $amount = $transaction->clientRefuelings->sum('total_amount');
            } elseif ($transaction->cash_liters > 0) {
                // كاش: نحسب السعر الأساسي × اللترات
                $basePrice = $transaction->nozzle->pump->tank->fuel->price_per_liter ?? 0;
                $amount = $transaction->cash_liters * $basePrice;
            }
            
            return [
                'date' => $transaction->created_at,
                'category' => 'إيراد',
                'type' => $transaction->nozzle->pump->tank->fuel->name ?? 'غير محدد',
                'description' => 'بيع وقود - ' . ($transaction->client_id ? 'آجل (' . $transaction->client->name . ')' : 'نقدي'),
                'amount' => $amount,
                'is_revenue' => true
            ];
        });
        $clientPayments = collect([]);
        if ($paymentType == 'all' || $paymentType == 'cash') {
            $clientPayments = TreasuryTransaction::where('type', 'income')->where('category', 'دفعة عميل')->whereDate('transaction_date', '>=', $startDate)->whereDate('transaction_date', '<=', $endDate)->get()->map(function ($payment) {
                return ['date' => $payment->transaction_date, 'category' => 'إيراد', 'type' => 'دفعة عميل', 'description' => $payment->description, 'amount' => $payment->amount, 'is_revenue' => true];
            });
        }
        $expenses = collect([]);
        if ($paymentType == 'all' || $paymentType == 'cash') {
            $expenses = TreasuryTransaction::where('type', 'expense')->whereDate('transaction_date', '>=', $startDate)->whereDate('transaction_date', '<=', $endDate)->get()->map(function ($expense) {
                return ['date' => $expense->transaction_date, 'category' => 'مصروف', 'type' => $expense->category, 'description' => $expense->description, 'amount' => $expense->amount, 'is_revenue' => false];
            });
        }
        $allTransactions = $revenueItems->concat($clientPayments)->concat($expenses)->sortBy('date');
        $balance = 0; $reportData = []; $totalRevenue = 0; $totalExpense = 0; $revenueByType = []; $expenseByCategory = [];
        foreach ($allTransactions as $item) {
            if ($item['is_revenue']) { $balance += $item['amount']; $totalRevenue += $item['amount']; if (!isset($revenueByType[$item['type']])) $revenueByType[$item['type']] = 0; $revenueByType[$item['type']] += $item['amount']; } else { $balance -= $item['amount']; $totalExpense += $item['amount']; if (!isset($expenseByCategory[$item['type']])) $expenseByCategory[$item['type']] = 0; $expenseByCategory[$item['type']] += $item['amount']; }
            $item['balance'] = $balance; $reportData[] = $item;
        }
        $netProfit = $totalRevenue - $totalExpense;
        return compact('reportData', 'totalRevenue', 'totalExpense', 'netProfit', 'capital', 'revenueByType', 'expenseByCategory', 'startDate', 'endDate', 'paymentType');
    }

    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('reports.index', $data);
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'pdf');
        $data = $this->getReportData($request);

        if ($type == 'excel') {
            return Excel::download(new FinancialReportExport($data['reportData'], $data['startDate'], $data['endDate'], $data['totalRevenue'], $data['totalExpense'], $data['netProfit']), 'financial-report.xlsx');
        } else {
            return $this->downloadPdf('reports.pdf', $data, 'financial-report.pdf');
        }
    }

    // --- REVENUES ---
    private function getRevenuesData(Request $request) {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $revenues = Transaction::with(['nozzle.pump.tank.fuel', 'shift.user', 'client', 'clientRefuelings'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()->get();
        
        $totalRevenue = 0;
        $cashRevenue = 0;
        $creditRevenue = 0;
        
        foreach ($revenues as $transaction) {
            if ($transaction->clientRefuelings->isNotEmpty()) {
                // آجل: ناخد السعر والمبلغ من client_refuelings
                $creditRevenue += $transaction->clientRefuelings->sum('total_amount');
            } elseif ($transaction->cash_liters > 0) {
                // كاش: نحسب السعر الأساسي × اللترات
                $basePrice = $transaction->nozzle->pump->tank->fuel->price_per_liter ?? 0;
                $cashRevenue += $transaction->cash_liters * $basePrice;
            }
        }
        
        $totalRevenue = $cashRevenue + $creditRevenue;
        
        return compact('revenues', 'totalRevenue', 'cashRevenue', 'creditRevenue', 'startDate', 'endDate');
    }

    public function revenues(Request $request) {
        return view('reports.revenues', $this->getRevenuesData($request));
    }

    public function exportRevenues(Request $request) {
        $type = $request->input('type', 'pdf');
        $data = $this->getRevenuesData($request);
        if ($type == 'excel') {
            return Excel::download(new GenericExport($data['revenues'], [['تقرير الإيرادات'], ['من ' . $data['startDate'] . ' إلى ' . $data['endDate']], [], ['التاريخ', 'نوع الوقود', 'الكمية', 'السعر', 'الإجمالي', 'العميل', 'الموظف']], function($row) {
                return [$row->created_at->format('Y-m-d H:i'), $row->nozzle->pump->tank->fuel->name ?? '-', $row->liters, $row->price_per_liter, $row->total_amount, $row->client->name ?? 'نقدي', $row->shift->user->name ?? '-'];
            }, 'الإيرادات'), 'revenues-report.xlsx');
        } else {
            return $this->downloadPdf('reports.revenues_pdf', $data, 'revenues-report.pdf');
        }
    }

    // --- EXPENSES ---
    private function getExpensesData(Request $request) {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $expenses = TreasuryTransaction::with('user')->where('type', 'expense')->whereDate('transaction_date', '>=', $startDate)->whereDate('transaction_date', '<=', $endDate)->latest('transaction_date')->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(function ($group) { return $group->sum('amount'); });
        return compact('expenses', 'totalExpenses', 'expensesByCategory', 'startDate', 'endDate');
    }

    public function expenses(Request $request) {
        return view('reports.expenses', $this->getExpensesData($request));
    }

    public function exportExpenses(Request $request) {
        $type = $request->input('type', 'pdf');
        $data = $this->getExpensesData($request);
        if ($type == 'excel') {
            return Excel::download(new GenericExport($data['expenses'], [['تقرير المصروفات'], ['من ' . $data['startDate'] . ' إلى ' . $data['endDate']], [], ['التاريخ', 'الفئة', 'البيان', 'المبلغ', 'المسؤول']], function($row) {
                return [$row->transaction_date, $row->category, $row->description, $row->amount, $row->user->name ?? '-'];
            }, 'المصروفات'), 'expenses-report.xlsx');
        } else {
            return $this->downloadPdf('reports.expenses_pdf', $data, 'expenses-report.pdf');
        }
    }

    // --- CLIENTS ---
    private function getClientsData(Request $request) {
        $clients = Client::with(['transactions', 'pump.tank.fuel'])->where('is_active', true)->get()->map(function ($client) {
            $totalTransactions = $client->transactions->sum('total_amount');
            $totalPayments = TreasuryTransaction::where('type', 'income')->where('category', 'دفعة عميل')->where('description', 'like', '%' . $client->name . '%')->sum('amount');
            $balance = $totalTransactions - $totalPayments;
            return ['id' => $client->id, 'name' => $client->name, 'fuel_type' => $client->pump->tank->fuel->name ?? 'غير محدد', 'total_transactions' => $totalTransactions, 'total_payments' => $totalPayments, 'balance' => $balance];
        })->filter(function ($client) { return $client['balance'] > 0; })->sortByDesc('balance');
        $totalDebt = $clients->sum('balance');
        return compact('clients', 'totalDebt');
    }

    public function clients(Request $request) {
        return view('reports.clients', $this->getClientsData($request));
    }

    public function exportClients(Request $request) {
        $type = $request->input('type', 'pdf');
        $data = $this->getClientsData($request);
        if ($type == 'excel') {
            return Excel::download(new GenericExport($data['clients'], [['تقرير ديون العملاء'], [], ['العميل', 'نوع الوقود', 'إجمالي المسحوبات', 'إجمالي السدادات', 'الرصيد المتبقي (مديونية)']], function($row) {
                return [$row['name'], $row['fuel_type'], $row['total_transactions'], $row['total_payments'], $row['balance']];
            }, 'العملاء'), 'clients-report.xlsx');
        } else {
            return $this->downloadPdf('reports.clients_pdf', $data, 'clients-report.pdf');
        }
    }

    // Helper for PDF
    private function downloadPdf($view, $data, $filename) {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'P', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $html = view($view, $data)->render();
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
