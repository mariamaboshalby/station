<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shift;
use App\Models\Pump;
use App\Models\Client;
use App\Models\ClientRefueling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index()
    {
        // جلب كل العمليات مع العلاقات المطلوبة
        $transactions = Transaction::with(['shift.user', 'pump.tank.fuel', 'client'])
            ->latest()
            ->get(); // ✅ كده رجع Collection

        // حساب إجمالي السعر لكل عملية
        $transactions->transform(function ($t) {
            $t->total_amount = $t->credit_liters * $t->pump->tank->fuel->price_per_liter;
            return $t;
        });

        return view('transactions.index', compact('transactions'));
    }



    public function create()
    {
        // 🔹 جلب الطلمبات حسب صلاحيات المستخدم
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

        // 🔹 جلب الشيفتات
        if (auth()->user()->hasRole('admin')) {
            $shifts = Shift::with('user')->latest()->get();
        } else {
            $shifts = Shift::with('user')
                ->where('user_id', auth()->id())
                ->whereNull('end_time')
                ->latest()
                ->get();
        }

        // 🔹 جلب العملاء
        $clients = Client::all();

        // 🔹 تحديد الشيفت الحالي
        $shift = $shifts->first();

        return view('transactions.create', compact('clients', 'pumps', 'shift', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'pump_id' => 'required|exists:pumps,id',
            'credit_liters' => 'required|numeric|min:0.01',
            'image' => 'required|image',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // 🔹 جلب الطلمبة وسعر اللتر
        $pump = Pump::with('tank.fuel')->findOrFail($validated['pump_id']);
        $fuelPrice = $pump->tank->fuel->price_per_liter;

        // 🔹 رفع الصورة
        $imagePath = $request->file('image')->store('transactions', 'public');

        // 🔹 حفظ العملية في جدول transactions
        $transaction = Transaction::create([
            'shift_id' => $validated['shift_id'],
            'pump_id' => $validated['pump_id'],
            'client_id' => $validated['client_id'] ?? null,
            'credit_liters' => $validated['credit_liters'],
            'image' => $imagePath,
            'notes' => $validated['notes'] ?? null,
            'operation_type' => 'آجل',
        ]);

        // 🔹 لو العملية تخص عميل آجل
        if (!empty($validated['client_id'])) {
            $client = Client::findOrFail($validated['client_id']);
            $totalAmount = $validated['credit_liters'] * $fuelPrice;

            // حفظ سجل التفويلة
            ClientRefueling::create([
                'client_id' => $validated['client_id'],
                'shift_id' => $validated['shift_id'],
                'transaction_id' => $transaction->id,
                'liters' => $validated['credit_liters'],
                'price_per_liter' => $fuelPrice,
                'total_amount' => $totalAmount,
            ]);

            // تحديث العميل في جدول clients
            $client->update([
                'liters_drawn' => $client->liters_drawn + $validated['credit_liters'],
                'total_price' => $client->total_price + $totalAmount,
                'rest' => $client->amount_paid - ($client->total_price + $totalAmount),
            ]);
        }
        return redirect()->route('transactions.create')->with('success', 'تم حفظ العملية وتحديث التانك بنجاح ✅');


    }


    public function destroy(Transaction $transaction)
    {
        if ($transaction->image && Storage::disk('public')->exists($transaction->image)) {
            Storage::disk('public')->delete($transaction->image);
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'تم حذف العملية بنجاح 🗑️');
    }
}
