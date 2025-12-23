<?php

namespace App\Http\Controllers;

use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TreasuryExport; // Assuming we will create this

class TreasuryController extends Controller
{
    private function getData(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $viewAll = $request->input('view_all', false);
        
        // 0. رأس المال
        $capital = TreasuryTransaction::where('type', 'income')
            ->where('category', 'رأس المال')
            ->sum('amount');
        
        // 1. حساب الرصيد الافتتاحي (ما قبل هذا التاريخ)
        if ($viewAll) {
            $openingBalance = 0;
        } else {
            $previousIncome = TreasuryTransaction::where('type', 'income')
                ->whereDate('transaction_date', '<', $date)
                ->sum('amount');
                
            $previousExpense = TreasuryTransaction::where('type', 'expense')
                ->whereDate('transaction_date', '<', $date)
                ->sum('amount');
                
            $previousSales = Transaction::whereNull('client_id')
                ->whereDate('created_at', '<', $date)
                ->sum('total_amount');
                
            $openingBalance = $previousIncome + $previousSales - $previousExpense;
        }

        // 2. جلب جميع العمليات المالية اليومية
        $treasuryQuery = TreasuryTransaction::with('user');
        $salesQuery = Transaction::with(['nozzle.pump.tank.fuel', 'shift.user'])->whereNull('client_id');
        
        if (!$viewAll) {
            $treasuryQuery->whereDate('transaction_date', $date);
            $salesQuery->whereDate('created_at', $date);
        }

        $treasuryTransactions = $treasuryQuery->latest('transaction_date')->get()->map(function($t) {
            return [
                'id' => 'treasury_' . $t->id,
                'type' => $t->type,
                'category' => $t->category,
                'description' => $t->description,
                'amount' => $t->amount,
                'date' => $t->transaction_date,
                'user' => $t->user->name ?? 'غير محدد',
                'source' => 'treasury'
            ];
        });

        $salesTransactions = $salesQuery->latest()->get()->map(function($t) {
            return [
                'id' => 'sale_' . $t->id,
                'type' => 'income',
                'category' => 'مبيعات ' . ($t->nozzle->pump->tank->fuel->name ?? 'وقود'),
                'description' => 'بيع نقدي - شيفت #' . $t->shift_id,
                'amount' => $t->total_amount,
                'date' => $t->created_at,
                'user' => $t->shift->user->name ?? 'غير محدد',
                'source' => 'sales'
            ];
        });
        
        $allTransactions = $treasuryTransactions->concat($salesTransactions)->sortByDesc('date');

        // 3. حساب الإجماليات
        $todayIncome = $allTransactions->where('type', 'income')->sum('amount');
        $todayExpense = $allTransactions->where('type', 'expense')->sum('amount');
        $currentBalance = $openingBalance + $todayIncome - $todayExpense;

        return compact(
            'date', 'viewAll', 'capital', 'openingBalance', 
            'allTransactions', 'todayIncome', 'todayExpense', 'currentBalance'
        );
    }

    public function index(Request $request)
    {
        $data = $this->getData($request);
        return view('treasury.index', $data);
    }

    public function export(Request $request) 
    {
        $type = $request->input('type', 'pdf');
        $data = $this->getData($request);

        if ($type == 'excel') {
             // سنقوم بإنشاء كلاس التصدير هذا للتعامل مع البيانات المدمجة
             // لتبسيط الأمر، سأستخدم ميزة التصدير المباشر من Collection أو إنشاء كلاس سريع
             return Excel::download(new \App\Exports\TreasuryExport($data), 'treasury-report.xlsx');
        } else {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8', 
                'format' => 'A4', 
                'orientation' => 'P',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ]);
            
            $html = view('treasury.pdf', $data)->render();
            $mpdf->WriteHTML($html);
            
            return response($mpdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="treasury-report.pdf"');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        TreasuryTransaction::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('treasury.index', ['date' => $validated['transaction_date']])
            ->with('success', 'تمت إضافة العملية بنجاح ✅');
    }

    public function destroy($id)
    {
        $transaction = TreasuryTransaction::findOrFail($id);
        $transaction->delete();

        return back()->with('success', 'تم حذف العملية بنجاح 🗑️');
    }
}
