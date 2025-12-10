<?php

namespace App\Http\Controllers;

use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TreasuryController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // 0. رأس المال (أول إيراد بتصنيف "رأس المال")
        $capital = TreasuryTransaction::where('type', 'income')
            ->where('category', 'رأس المال')
            ->sum('amount');
        
        // 1. حساب الرصيد الافتتاحي (ما قبل هذا التاريخ)
        $previousIncome = TreasuryTransaction::where('type', 'income')
            ->whereDate('transaction_date', '<', $date)
            ->sum('amount');
            
        $previousExpense = TreasuryTransaction::where('type', 'expense')
            ->whereDate('transaction_date', '<', $date)
            ->sum('amount');
            
        $openingBalance = $previousIncome - $previousExpense;

        // 2. حركات اليوم المحدد
        $todayTransactions = TreasuryTransaction::with('user')
            ->whereDate('transaction_date', $date)
            ->latest()
            ->get();

        $todayIncome = $todayTransactions->where('type', 'income')->sum('amount');
        $todayExpense = $todayTransactions->where('type', 'expense')->sum('amount');

        // 3. الرصيد الحالي (الافتتاحي + إيراد اليوم - مصروف اليوم)
        $currentBalance = $openingBalance + $todayIncome - $todayExpense;

        return view('treasury.index', compact(
            'date',
            'capital',
            'openingBalance',
            'todayTransactions',
            'todayIncome',
            'todayExpense',
            'currentBalance'
        ));
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
