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
    public function index(Request $request)
    {
        $query = Transaction::with(['shift.user', 'pump.tank.fuel', 'client', 'media'])->latest();

        // تصفية حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // تصفية حسب الموظف
        if ($request->filled('user_id')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // تصفية حسب التاريخ من
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // تصفية حسب التاريخ إلى
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->get();

        // حساب المبلغ الإجمالي
        $transactions->transform(function ($t) {
            $price = $t->pump->tank->fuel->price_per_liter ?? 0;
            $t->total_amount = ($t->credit_liters + $t->cash_liters) * $price;
            return $t;
        });

        $clients = Client::all();
        $users = \App\Models\User::all();

        return view('transactions.index', compact('transactions', 'clients', 'users'));
    }

    // نموذج إنشاء عملية جديدة
    public function create()
    {
        // 🔹 المسدسات المتاحة
        if (auth()->user()->hasRole('admin')) {
            $nozzles = \App\Models\Nozzle::with(['pump.tank.fuel'])->get();
        } else {
            // جلب المسدسات المسموح بها للمستخدم
            $userPumpIds = auth()->user()->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

            $nozzles = \App\Models\Nozzle::with(['pump.tank.fuel'])
                ->whereIn('pump_id', $userPumpIds)
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

        // 🔹 العملاء (النشطين فقط)
        $clients = Client::where('is_active', true)->get();

        // 🔹 تحديد الشيفت الحالي
        $shift = $shifts->first();

        return view('transactions.create', compact('clients', 'nozzles', 'shift', 'shifts'));
    }

    // حفظ العملية
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'nozzle_id' => 'required|exists:nozzles,id',
            'credit_liters' => 'required|numeric|min:0.01',
            'vehicle_number' => 'nullable|string|max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'notes' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // 🔹 جلب بيانات المسدس ومنها الطلمبة وسعر اللتر
        $nozzle = \App\Models\Nozzle::with('pump.tank.fuel')->findOrFail($validated['nozzle_id']);
        $pump = $nozzle->pump;
        $fuelPrice = $pump->tank->fuel->price_per_liter ?? 0;

        // 🔹 حساب المجموع الكلي
        $totalAmount = $validated['credit_liters'] * $fuelPrice;

        // 🔹 إنشاء العملية
        $transaction = Transaction::create([
            'shift_id' => $validated['shift_id'],
            'pump_id' => $pump->id,
            'nozzle_id' => $nozzle->id,
            'client_id' => $validated['client_id'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'credit_liters' => $validated['credit_liters'],
            'notes' => $validated['notes'] ?? null,
            'operation_type' => 'آجل',
            'total_amount' => $totalAmount,
        ]);

        // 🔹 حفظ الصورة باستخدام Spatie في فولدر public
        if ($request->hasFile('image')) {
            $transaction->addMediaFromRequest('image')->toMediaCollection('transactions', 'public'); 
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