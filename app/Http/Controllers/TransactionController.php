<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shift;
use App\Models\Nozzle;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // عرض كل العمليات
    public function index()
    {
        $transactions = Transaction::with(['shift.user', 'nozzle.tank.fuel'])->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    // فورم إضافة عملية بيع
    public function create()
    {
        $shifts = Shift::whereNull('end_time')->with('user')->get();
        $nozzles = Nozzle::with('tank.fuel')->get();
        return view('transactions.create', compact('shifts', 'nozzles'));
    }

    // تخزين العملية
    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required',
            'nozzle_id' => 'required',
            'liters_dispensed' => 'required|numeric',
        ]);

        $nozzle = Nozzle::with('tank.fuel')->findOrFail($request->nozzle_id);

        // نفترض إن سعر اللتر في جدول fuel
        $pricePerLiter = $nozzle->tank->fuel->price_per_liter;

        $totalPrice = $request->liters_dispensed * $pricePerLiter;

        Transaction::create([
            'shift_id' => $request->shift_id,
            'nozzle_id' => $request->nozzle_id,
            'liters_dispensed' => $request->liters_dispensed,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'تم تسجيل العملية بنجاح');
    }

}
