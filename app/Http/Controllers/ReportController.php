<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\TreasuryTransaction;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // تحديد الفترة (من - إلى) أو افتراضياً الشهر الحالي
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // تحديد نوع الدفع (الكل - نقدي - آجل)
        $paymentType = $request->input('payment_type', 'all');
        
        // 0. رأس المال
        $capital = TreasuryTransaction::where('type', 'income')
            ->where('category', 'رأس المال')
            ->sum('amount');

        // 1. جلب الإيرادات (المعاملات) - مع فلترة الكاش والآجل
        $revenues = Transaction::with(['nozzle.pump.tank.fuel', 'shift'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($paymentType == 'credit', function ($q) {
                return $q->whereNotNull('client_id');
            })
            ->when($paymentType == 'cash', function ($q) {
                // الكاش هو ما ليس له عميل
                return $q->whereNull('client_id');
            })
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => $transaction->created_at,
                    'category' => 'إيراد',
                    'type' => $transaction->nozzle->pump->tank->fuel->name ?? 'غير محدد',
                    'description' => 'بيع وقود - ' . ($transaction->client_id ? 'آجل' : 'نقدي'),
                    'amount' => $transaction->total_amount,
                    'is_revenue' => true,
                ];
            });

        // 1.5 جلب دفعات العملاء من الخزنة (تظهر في الكاش فقط)
        $clientPayments = collect([]);
        if ($paymentType == 'all' || $paymentType == 'cash') {
            $clientPayments = TreasuryTransaction::where('type', 'income')
                ->where('category', 'دفعة عميل')
                ->whereDate('transaction_date', '>=', $startDate)
                ->whereDate('transaction_date', '<=', $endDate)
                ->get()
                ->map(function ($payment) {
                    return [
                        'date' => $payment->transaction_date,
                        'category' => 'إيراد',
                        'type' => 'دفعة عميل',
                        'description' => $payment->description,
                        'amount' => $payment->amount,
                        'is_revenue' => true,
                    ];
                });
        }

        // 2. جلب المصروفات (تظهر فقط في حالة "الكل" أو "نقدي")
        $expenses = collect([]);
        if ($paymentType == 'all' || $paymentType == 'cash') {
            $expenses = Expense::whereDate('expense_date', '>=', $startDate)
                ->whereDate('expense_date', '<=', $endDate)
                ->get()
                ->map(function ($expense) {
                    return [
                        'date' => $expense->expense_date,
                        'category' => 'مصروف',
                        'type' => $expense->category_label,
                        'description' => $expense->description,
                        'amount' => $expense->amount,
                        'is_revenue' => false,
                    ];
                });
        }

        // 3. دمج البيانات وترتيبها زمنياً
        $allTransactions = $revenues->concat($clientPayments)->concat($expenses)->sortBy('date');

        // 4. حساب الرصيد التراكمي والإجماليات
        $balance = 0;
        $reportData = [];
        
        $totalRevenue = 0;
        $totalExpense = 0;

        // تجميع حسب النوع والفئة للملخص
        $revenueByType = [];
        $expenseByCategory = [];

        foreach ($allTransactions as $item) {
            if ($item['is_revenue']) {
                $balance += $item['amount'];
                $totalRevenue += $item['amount'];
                
                // تجميع الإيرادات
                if (!isset($revenueByType[$item['type']])) $revenueByType[$item['type']] = 0;
                $revenueByType[$item['type']] += $item['amount'];

            } else {
                $balance -= $item['amount'];
                $totalExpense += $item['amount'];

                // تجميع المصروفات
                if (!isset($expenseByCategory[$item['type']])) $expenseByCategory[$item['type']] = 0;
                $expenseByCategory[$item['type']] += $item['amount'];
            }

            $item['balance'] = $balance;
            $reportData[] = $item;
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('reports.index', compact(
            'reportData', 
            'totalRevenue', 
            'totalExpense', 
            'netProfit',
            'capital',
            'revenueByType', 
            'expenseByCategory',
            'startDate',
            'endDate',
            'paymentType'
        ));
    }

    // صفحة تفاصيل الإيرادات
    public function revenues(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $revenues = Transaction::with(['nozzle.pump.tank.fuel', 'shift.user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $totalRevenue = $revenues->sum('total_amount');

        return view('reports.revenues', compact('revenues', 'totalRevenue', 'startDate', 'endDate'));
    }
}
