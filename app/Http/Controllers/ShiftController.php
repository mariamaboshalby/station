<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // عرض كل الشيفتات
    public function index()
    {
        // بنجيب الشيفتات زي ما هي بدون ما نغير التواريخ
        $shifts = Shift::with('user')->latest()->get();

        return view('shifts.index', compact('shifts'));
    }

    // فورم إضافة شيفت
    public function create()
    {
        $users = User::all();
        return view('shifts.create', compact('users'));
    }

    // تخزين شيفت جديد
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'nullable|date',
        ]);

        Shift::create([
            'user_id' => $request->user_id,
            // لو المدخلش وقت → نحط الوقت الحالي بتوقيت القاهرة
            'start_time' => $request->start_time
                ? Carbon::parse($request->start_time)
                : now(), // now() بياخد التوقيت من config/app.php
            'end_time' => null,
        ]);

        return redirect()->route('shifts.index')->with('success', 'تم فتح الشيفت');
    }

    // إغلاق شيفت
    public function close($id)
    {
        $shift = Shift::findOrFail($id);
        $now = now()->setTimezone('Africa/Cairo');
        $shift->update([
            'end_time' => $now
        ]);

        return redirect()->route('shifts.index')->with('success', 'تم إغلاق الشيفت');
    }

    // تقرير شيفت
    public function report($id)
    {
        $shift = Shift::with(['transactions.nozzle.tank.fuel'])->findOrFail($id);

        return view('shifts.report', compact('shift'));
    }
}
