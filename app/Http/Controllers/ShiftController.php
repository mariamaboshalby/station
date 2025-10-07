<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\Transaction;
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
        return view('shifts.create', compact('users'));
    }

    // حفظ فتح شيفت
    public function store(Request $request)
    {
        if (auth()->user()->hasRole('admin')) {
            $request->validate([
                'user_id'       => 'required|exists:users,id',
                'meter_reading' => 'required|numeric|min:0',
                'meter_image'   => 'required|image',
            ]);
            $userId = $request->user_id;
        } else {
            $request->validate([
                'meter_reading' => 'required|numeric|min:0',
                'meter_image'   => 'required|image',
            ]);
            $userId = auth()->id();
        }

        // 🟢 رفع صورة العداد
        $imagePath = $request->file('meter_image')->store('meter_images', 'public');

        // 🟡 تحديد تطابق العداد تلقائيًا
        $lastShift = Shift::where('user_id', $userId)->latest()->first();
        $meterMatch = 1; // افتراضي مطابقة
        if ($lastShift && $request->meter_reading <= $lastShift->meter_reading) {
            $meterMatch = 0; // غير مطابقة لو القراءة أقل
        }

        // 🟢 إنشاء الشيفت الجديد
        $shift = Shift::create([
            'user_id'        => $userId,
            'meter_reading'  => $request->meter_reading,
            'meter_image'    => $imagePath,
            'meter_match'    => $meterMatch,
            'start_time'     => now(),
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
        $shift = Shift::findOrFail($id);

        $request->validate([
            'end_meter_reading' => 'required|numeric|min:0',
            'end_meter_image'   => 'required|image',
            'notes'             => 'nullable|string|max:1000',
        ]);

        // 🟢 رفع صورة نهاية العداد
        $imagePath = $request->file('end_meter_image')->store('meter_images', 'public');

        // 🟡 حساب المبيعات من العمليات
        $cashSales = Transaction::where('shift_id', $shift->id)->sum('cash_amount');
        $creditSales = Transaction::where('shift_id', $shift->id)->sum('credit_amount');
        $totalSales = $cashSales + $creditSales;

        // 🟢 تحديث بيانات الشيفت
        $shift->update([
            'end_meter_reading' => $request->end_meter_reading,
            'end_meter_image'   => $imagePath,
            'notes'             => $request->notes,
            'end_time'          => now(),
            'cash_sales'        => $cashSales,
            'credit_sales'      => $creditSales,
            'total_sales'       => $totalSales,
        ]);

        // 🟣 توجيه حسب الدور
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('shifts.index')->with('success', 'تم إغلاق الشيفت بنجاح ✅');
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
