<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Pump;
use App\Models\Tank;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // عرض كل الشيفتات
    public function index()
    {
        $shifts = Shift::with('user')->latest()->paginate();
        return view('shifts.index', compact('shifts'));
    }

    // فورم فتح شيفت
    public function create()
    {
        $users = auth()->user()->hasRole('admin') ? User::all() : collect();

        // 🔹 لو المستخدم مش أدمن، نجلب الطلمبات اللي عنده صلاحية ليها
        if (auth()->user()->hasRole('admin')) {
            $pumps = Pump::with('tank')->get();
        } else {
            $userPumpIds = auth()->user()->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

            $pumps = Pump::with('tank')->whereIn('id', $userPumpIds)->get();
        }

        // 🔹 حساب مجموع اللترات المسحوبة من التانكات المرتبطة بهذه الطلمبات فقط
        $tankIds = $pumps->pluck('tank.id')->filter()->unique();
        $totalLitersDrawn = Tank::whereIn('id', $tankIds)->sum('liters_drawn');

        return view('shifts.create', compact('users', 'totalLitersDrawn'));
    }

    // حفظ فتح شيفت
    public function store(Request $request)
    {
        if (auth()->user()->hasRole('admin')) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'meter_reading' => 'required|numeric|min:0',
                'meter_image' => 'required|image',
            ]);
            $userId = $request->user_id;
        } else {
            $request->validate([
                'meter_reading' => 'required|numeric|min:0',
                'meter_image' => 'required|image',
            ]);
            $userId = auth()->id();
        }

        // 🟢 رفع صورة العداد
        $imagePath = $request->file('meter_image')->store('meter_images', 'public');

        // 🟢 حفظ الشيفت الجديد
        $shift = Shift::create([
            'user_id' => $userId,
            'meter_reading' => $request->meter_reading,
            'meter_image' => $imagePath,
            'meter_match' => $request->meter_match,
            'start_time' => now(),
        ]);

        return redirect()->route('transactions.create', ['shift_id' => $shift->id])
            ->with('success', 'تم فتح الشيفت بنجاح، يمكنك إضافة العمليات الآن ✅');
    }

    // فورم إغلاق شيفت
    public function close($id)
    {
        $shift = Shift::findOrFail($id);
        return view('shifts.close', compact('shift'));
    }

    // حفظ إغلاق الشيفت
    public function closeStore(Request $request, $id)
    {
        $shift = Shift::with('transactions')->findOrFail($id);

        $request->validate([
            'end_meter_reading' => 'required|numeric|min:0',
            'end_meter_image' => 'required|image',
            'notes' => 'nullable|string|max:1000',
        ]);

        $imagePath = $request->file('end_meter_image')->store('meter_images', 'public');

        // ✅ حساب إجمالي اللترات الآجلة من العمليات
        $totalCreditLiters = $shift->transactions->sum('credit_liters');

        // ✅ حساب الكاش ليتر من قراءة العداد
        $cashLiters = $request->end_meter_reading - ($shift->meter_reading + $totalCreditLiters);

        // ✅ جلب أول طلمبة يملكها المستخدم
        $userPumpIds = auth()->user()->getPermissionNames()
            ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
            ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

        $pump = Pump::with('tank.fuel')
            ->whereIn('id', $userPumpIds)
            ->first();

        $fuelPrice = $pump?->tank?->fuel?->price_per_liter ?? 0;
        $totalAmount = ($cashLiters + $totalCreditLiters) * $fuelPrice;

        // ✅ إنشاء عملية جديدة
        Transaction::create([
            'shift_id' => $shift->id,
            'pump_id' => $pump->id,
            'cash_liters' => $cashLiters,
            'credit_liters' => $totalCreditLiters,
            'total_amount' => $totalAmount,
            'image' => $imagePath,
        ]);
        
        // 🛢️ تحديث بيانات التانك
        if ($pump && $pump->tank) {
            $tank = $pump->tank;
            $litersUsed = $cashLiters + $totalCreditLiters;

            // زيادة اللترات المسحوبة
            $tank->liters_drawn += $litersUsed;

            // تقليل الكمية الحالية في التانك
            $tank->current_level -= $litersUsed;

            // حفظ التحديثات
            $tank->save();
        }

        // ✅ تحديث بيانات الشيفت
        $shift->update([
            'end_meter_reading' => $request->end_meter_reading,
            'end_meter_image' => $imagePath,
            'notes' => $request->notes,
            'end_time' => now(),
            'cash_sales' => $cashLiters,
            'credit_sales' => $totalCreditLiters,
        ]);

        // ✅ إعادة التوجيه
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('shifts.index')->with('success', 'تم إغلاق الشيفت وحساب الكاش بنجاح ✅');
        }

        auth()->logout();
        return redirect()->route('login')->with('success', 'تم إغلاق الشيفت وتسجيل الخروج بنجاح ✅');
    }

    // تقرير شيفت
    public function report($id)
    {
        $shift = Shift::with(['transactions.pump.tank.fuel', 'user'])->find($id);
        if (!$shift) {
            return redirect()->back()->with('error', 'الشيفت غير موجود ❌');
        }

        return view('shifts.report', compact('shift'));
    }

    // عرض كل شيفتات موظف
    public function userShifts($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'الموظف غير موجود ❌');
        }

        $shifts = Shift::where('user_id', $id)
            ->with(['transactions.pump.tank.fuel', 'user'])
            ->latest()
            ->get();

        return view('users.report', compact('user', 'shifts'));
    }
}
