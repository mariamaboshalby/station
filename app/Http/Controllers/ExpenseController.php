<?php

namespace App\Http\Controllers;

use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $expenses = TreasuryTransaction::with('user')
            ->where('type', 'expense')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->latest('transaction_date')
            ->get();

        return view('expenses.index', compact('expenses', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        TreasuryTransaction::create([
            'user_id' => auth()->id(),
            'type' => 'expense',
            'category' => $request->category,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        return redirect()->route('expenses.index')->with('success', 'تم تسجيل المصروف بنجاح');
    }

    public function destroy($id)
    {
        $transaction = TreasuryTransaction::findOrFail($id);
        $transaction->delete();
        
        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }
}
