<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shift;
use App\Models\Pump;
use App\Models\Client;
use App\Models\ClientRefueling;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // عرض كل العمليات
    public function index()
    {
        $transactions = Transaction::with(['shift.user', 'pump.tank.fuel', 'client', 'media'])
            ->latest()
            ->get();

        // حساب المبلغ الإجمالي
        $transactions->transform(function ($t) {
            $price = $t->pump->tank->fuel->price_per_liter ?? 0;
            $t->total_amount = ($t->credit_liters + $t->cash_liters) * $price;
            return $t;
        });

        return view('transactions.index', compact('transactions'));
    }

    // نموذج إنشاء عملية جديدة
    public function create()
    {
        // 🔹 الطلمبات
        if (auth()->user()->hasRole('admin')) {
            $pumps = Pump::with('tank.fuel')->get();
        } else {
            $userPermissions = auth()->user()->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

            $pumps = Pump::with('tank.fuel')
                ->whereIn('id', $userPermissions)
                ->get();
        }

        // 🔹 الشيفتات
        if (auth()->user()->hasRole('admin')) {
            $shifts = Shift::with('user')->latest()->get();
        } else {
            $shifts = Shift::with('user')
                ->where('user_id', auth()->id())
                ->whereNull('end_time')
                ->latest()
                ->get();
        }

        // 🔹 العملاء
        $clients = Client::all();

        // 🔹 تحديد الشيفت الحالي
        $shift = $shifts->first();

        return view('transactions.create', compact('clients', 'pumps', 'shift', 'shifts'));
    }

    // حفظ العملية
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'pump_id' => 'required|exists:pumps,id',
            'credit_liters' => 'required|numeric|min:0.01',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'notes' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // 🔹 جلب الطلمبة وسعر اللتر
        $pump = Pump::with('tank.fuel')->findOrFail($validated['pump_id']);
        $fuelPrice = $pump->tank->fuel->price_per_liter ?? 0;

        // 🔹 حساب المجموع الكلي
        $totalAmount = $validated['credit_liters'] * $fuelPrice;

        // 🔹 إنشاء العملية أولًا
        $transaction = Transaction::create([
            'shift_id' => $validated['shift_id'],
            'pump_id' => $validated['pump_id'],
            'client_id' => $validated['client_id'] ?? null,
            'credit_liters' => $validated['credit_liters'],
            'notes' => $validated['notes'] ?? null,
            'operation_type' => 'آجل',
            'total_amount' => $totalAmount,
        ]);

 // 🔹 حفظ الصورة باستخدام Spatie في فولدر public/uploads
    if ($request->hasFile('image')) {
        $transaction->addMediaFromRequest('image')->toMediaCollection('transactions', 'uploads'); // 'uploads' هو الـ disk الجديد
        }

        // 🔹 لو العملية تخص عميل آجل
        if (!empty($validated['client_id'])) {
            $client = Client::findOrFail($validated['client_id']);

            ClientRefueling::create([
                'client_id' => $client->id,
                'shift_id' => $validated['shift_id'],
                'transaction_id' => $transaction->id,
                'liters' => $validated['credit_liters'],
                'price_per_liter' => $fuelPrice,
                'total_amount' => $totalAmount,
            ]);

            $client->update([
                'liters_drawn' => $client->liters_drawn + $validated['credit_liters'],
                'total_price' => $client->total_price + $totalAmount,
                'rest' => $client->amount_paid - ($client->total_price + $totalAmount),
            ]);
        }

        return redirect()->route('transactions.create')
            ->with('success', 'تم حفظ العملية والصورة بنجاح ✅');
    }

    // حذف العملية
    public function destroy(Transaction $transaction)
    {
        // حذف الصور تلقائيًا من Spatie
        $transaction->clearMediaCollection('transactions');

        $transaction->delete();

        return redirect()->back()->with('success', 'تم حذف العملية بنجاح 🗑');
    }
}