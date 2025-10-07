<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shift;
use App\Models\Pump;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // عرض كل العمليات
    public function index()
    {
        $transactions = Transaction::with(['shift.user', 'pump.tank.fuel', 'client'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    // عرض فورم إضافة عملية بيع آجل فقط
    public function create()
    {
        $user = auth()->user();

        // 🟢 الشيفتات المفتوحة فقط
        if ($user->hasRole('admin')) {
            $shifts = Shift::whereNull('end_time')->with('user')->get();
        } else {
            $shifts = Shift::where('user_id', $user->id)
                ->whereNull('end_time')
                ->with('user')
                ->get();
        }

        // 🟢 الطلمبات المسموح بها
        if ($user->hasRole('admin')) {
            $pumps = Pump::with(['tank.fuel'])->get();
        } else {
            $allowedPumps = $user->getAllPermissions()
                ->pluck('name')
                ->filter(fn($p) => str_starts_with($p, 'use_pump_'))
                ->map(fn($p) => (int) str_replace('use_pump_', '', $p))
                ->toArray();

            $pumps = Pump::whereIn('id', $allowedPumps)
                ->with(['tank.fuel'])
                ->get();
        }

        return view('transactions.create', compact('shifts', 'pumps'));
    }

    // تخزين العملية (بيع آجل)
    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'pump_id' => 'required|exists:pumps,id',
            'client_id' => 'nullable|exists:clients,id',
            'credit_liters' => 'nullable|numeric|min:0',
            'credit_amount' => 'nullable|numeric|min:0',
            'tank_level_after' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        $pump = Pump::with('tank.fuel')->findOrFail($request->pump_id);
        $tank = $pump->tank;

        // 🧮 حساب الكمية المسحوبة
        $oldLevel = $tank->current_level;
        $newLevel = $request->tank_level_after;
        $dispensedLiters = max(0, $oldLevel - $newLevel);

        $pricePerLiter = $pump->tank->fuel->price_per_liter;
        $cashAmount = $dispensedLiters * $pricePerLiter;

        // 🟢 رفع الصورة لو موجودة
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('transactions', 'public')
            : null;

        // 🟡 تحديد إذا كانت صورة العداد مطابقة أم لا
        $lastTransaction = Transaction::where('pump_id', $request->pump_id)
            ->latest()
            ->first();

        $meterMatch = 1; // القيمة الافتراضية: مطابقة
        if ($lastTransaction && $newLevel <= $lastTransaction->tank_level_after) {
            $meterMatch = 0; // غير مطابقة لو القراءة أقل أو مساوية للسابقة
        }

        // 🟢 تحديث مستوى التانك
        $tank->update([
            'current_level' => $newLevel,
        ]);

        // 🟢 إنشاء العملية
        Transaction::create([
            'shift_id' => $request->shift_id,
            'pump_id' => $request->pump_id,
            'client_id' => $request->client_id,
            'credit_liters' => $request->credit_liters ?? 0,
            'credit_amount' => $request->credit_amount ?? 0,
            'cash_liters' => $dispensedLiters,
            'cash_amount' => $cashAmount,
            'total_amount' => ($request->credit_amount ?? 0) + $cashAmount,
            'tank_level_after' => $newLevel,
            'meter_match' => $meterMatch,
            'image' => $imagePath,
            'notes' => $request->notes,
        ]);

            $user = auth()->user();
        if ($user->hasRole('admin')) {
            return redirect()->route('transactions.index')
                ->with('success', 'تم تسجيل العملية بنجاح ✅');
        } else {
             return redirect()->route('home.buttons')
                ->with('success', 'تم تسجيل العملية بنجاح ✅');
        }



    }


}
