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
        $shifts = Shift::with(['user', 'nozzleReadings'])->latest()->paginate();
        return view('shifts.index', compact('shifts'));
    }

    // فورم فتح شيفت
    public function create()
    {
        $users = auth()->user()->hasRole('admin') ? User::all() : collect();

        // 🔹 جلب المسدسات بدلاً من الطلمبات
        if (auth()->user()->hasRole('admin')) {
            $nozzles = \App\Models\Nozzle::with(['pump.tank.fuel'])->get();
        } else {
            // جلب الطلمبات المسموح بها للمستخدم
            $userPumpIds = auth()->user()->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

            // جلب المسدسات التابعة لهذه الطلمبات
            $nozzles = \App\Models\Nozzle::with(['pump.tank.fuel'])
                ->whereIn('pump_id', $userPumpIds)
                ->get();
        }

        // حساب إجمالي القراءات الحالية كقيمة افتراضية لبداية الشيفت
        $totalLitersDrawn = $nozzles->sum('meter_reading');

        return view('shifts.create', compact('users', 'nozzles', 'totalLitersDrawn'));
    }

        // حفظ فتح شيفت
    public function store(Request $request)
    {
        if (auth()->user()->hasRole('admin')) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'meter_match' => 'required|boolean',
                'captured_images_data' => 'required|string',
            ]);
            $userId = $request->user_id;

            // للأدمن: سنفترض جدولاً أنه يفتح شيفت لكل المسدسات (أو يمكن تعديله لاحقاً ليختار)
            // هنا سنجلب كل المسدسات
            $nozzles = \App\Models\Nozzle::all();

        } else {
            $request->validate([
                'meter_match' => 'required|boolean',
                'captured_images_data' => 'required|string',
            ]);
            $userId = auth()->id();

            // للمستخدم: جلب مسدساته فقط
            $userPumpIds = auth()->user()->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));
                
            $nozzles = \App\Models\Nozzle::whereIn('pump_id', $userPumpIds)->get();
        }

        // 🟢 حفظ الشيفت الجديد أولاً
        $shift = Shift::create([
            'user_id' => $userId,
            'meter_match' => $request->meter_match,
            'start_time' => now(),
        ]);

        // 🟢 حفظ قراءات المسدسات تلقائياً من القراءات الحالية في قاعدة البيانات
        foreach ($nozzles as $nozzle) {
            \App\Models\ShiftNozzleReading::create([
                'shift_id' => $shift->id,
                'nozzle_id' => $nozzle->id,
                'start_reading' => $nozzle->meter_reading, // استخدام القراءة الحالية المحفوظة
            ]);
        }

        // 🟢 حفظ الصور الملتقطة بالكاميرا
        if ($request->filled('captured_images_data')) {
            $capturedImages = json_decode($request->input('captured_images_data'), true);
            
            if (is_array($capturedImages)) {
                foreach ($capturedImages as $index => $imageData) {
                    try {
                        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                        $imageData = base64_decode($imageData);
                        
                        if ($imageData === false) {
                            continue; // Skip if base64 decode fails
                        }
                        
                        $tempFile = tempnam(sys_get_temp_dir(), 'captured_shift_start_image_' . $index . '_');
                        
                        if ($tempFile && file_put_contents($tempFile, $imageData) !== false) {
                            $shift->addMedia($tempFile)
                                ->usingFileName('captured_shift_start_photo_' . ($index + 1) . '_' . time() . '.jpg')
                                ->toMediaCollection('start_meter_images', 'public');
                                
                            // Clean up temporary file if it exists
                            if (file_exists($tempFile)) {
                                unlink($tempFile);
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue with other images
                        \Log::error('Error processing captured shift start image: ' . $e->getMessage());
                        continue;
                    }
                }
            }
        }

        return redirect()->route('transactions.create', ['shift_id' => $shift->id])
            ->with('success', 'تم فتح الشيفت بنجاح، يمكنك إضافة العمليات الآن ✅');
    }

    // فورم إغلاق شيفت
    public function close($id)
    {
        $shift = Shift::with('nozzleReadings.nozzle.pump')->findOrFail($id);
        $totalCreditLiters = $shift->transactions()->sum('credit_liters');

        return view('shifts.close', compact('shift', 'totalCreditLiters'));
    }
    public function closeStore(Request $request, $id)
    {
        $shift = Shift::with(['transactions', 'nozzleReadings.nozzle.pump.tank'])->findOrFail($id);
        $validated = $request->validate([
            'nozzle_end_readings' => 'required|array',
            'nozzle_end_readings.*' => 'required|numeric|min:0',
            'captured_images_data' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        \DB::beginTransaction();
        
        try {
            $totalLitersDispensed = 0;
            $pumpLiters = []; // لحفظ اللترات حسب كل طلمبة

            // ✅ حفظ قراءات النهاية وحساب اللترات
            foreach ($validated['nozzle_end_readings'] as $nozzleId => $endReading) {
                $shiftReading = \App\Models\ShiftNozzleReading::where('shift_id', $shift->id)
                    ->where('nozzle_id', $nozzleId)
                    ->first();

                if ($shiftReading) {
                    $litersDispensed = $endReading - $shiftReading->start_reading;
                    
                    $shiftReading->update([
                        'end_reading' => $endReading,
                        'liters_dispensed' => $litersDispensed,
                    ]);

                    // تحديث قراءة المسدس
                    $nozzle = \App\Models\Nozzle::find($nozzleId);
                    if ($nozzle) {
                        $nozzle->meter_reading = $endReading;
                        $nozzle->save();

                        // جمع اللترات حسب الطلمبة
                        $pumpId = $nozzle->pump_id;
                        if (!isset($pumpLiters[$pumpId])) {
                            $pumpLiters[$pumpId] = 0;
                        }
                        $pumpLiters[$pumpId] += $litersDispensed;
                    }

                    $totalLitersDispensed += $litersDispensed;
                }
            }

            // ✅ إجمالي اللترات الآجلة
            $totalCreditLiters = $shift->transactions->sum('credit_liters');

            // ✅ إنشاء عمليات لكل طلمبة (تجميعي)
            $totalShiftCashLiters = 0;
            $totalShiftAmount = 0;
            $firstPumpId = null;
            $usedPumpsNames = [];

            foreach ($pumpLiters as $pumpId => $liters) {
                $pump = Pump::with('tank.fuel')->find($pumpId);
                
                if ($pump) {
                    if (!$firstPumpId) $firstPumpId = $pump->id;
                    $usedPumpsNames[] = $pump->name;

                    $fuelPrice = $pump->tank->fuel->price_per_liter ?? 0;
                    $amount = $liters * $fuelPrice;

                    // تجميع الإجماليات
                    $totalShiftCashLiters += $liters;
                    $totalShiftAmount += $amount;

                    // ✅ تحديث بيانات التانك
                    if ($pump->tank) {
                        $tank = $pump->tank;
                        $tank->liters_drawn += $liters;
                        $tank->current_level -= $liters;
                        $tank->save();
                    }
                }
            }

            // ✅ إنشاء معاملة واحدة مجمعة للشيفت
            $shiftTransaction = null;
            if ($totalShiftCashLiters > 0 && $firstPumpId) {
                $shiftTransaction = Transaction::create([
                    'shift_id' => $shift->id,
                    'pump_id' => $firstPumpId, // تسجيلها باسم أول طلمبة
                    'cash_liters' => $totalShiftCashLiters,
                    'credit_liters' => 0,
                    'total_amount' => $totalShiftAmount,
                    'notes' => "إغلاق شيفت: " . implode(' + ', $usedPumpsNames),
                ]);
            }

            // ✅ حفظ الصور الملتقطة بالكاميرا
            if ($request->filled('captured_images_data')) {
                $capturedImages = json_decode($request->input('captured_images_data'), true);
                
                if (is_array($capturedImages)) {
                    foreach ($capturedImages as $index => $imageData) {
                        try {
                            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                            $imageData = base64_decode($imageData);
                            
                            if ($imageData === false) {
                                continue; // Skip if base64 decode fails
                            }
                            
                            $tempFile = tempnam(sys_get_temp_dir(), 'captured_shift_end_image_' . $index . '_');
                            
                            if ($tempFile && file_put_contents($tempFile, $imageData) !== false) {
                                $shift->addMedia($tempFile)
                                    ->usingFileName('captured_shift_end_photo_' . ($index + 1) . '_' . time() . '.jpg')
                                    ->toMediaCollection('end_meter_images', 'public');
                                    
                                // Clean up temporary file if it exists
                                if (file_exists($tempFile)) {
                                    unlink($tempFile);
                                }
                            }
                        } catch (\Exception $e) {
                            // Log error but continue with other images
                            \Log::error('Error processing captured shift image: ' . $e->getMessage());
                            continue;
                        }
                    }
                }
            }

            // ✅ تحديث بيانات الشيفت
            $shift->update([
                'notes' => $validated['notes'] ?? null,
                'end_time' => now(),
                'cash_sales' => $totalLitersDispensed,
                'credit_sales' => $totalCreditLiters,
            ]);

            \DB::commit();

            // ✅ التوجيه النهائي
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('shifts.index')
                    ->with('success', '✅ تم إغلاق الشيفت وتسجيل الصورة والعملية بنجاح.');
            }

            auth()->logout();
            return redirect()->route('login')
                ->with('success', '✅ تم إغلاق الشيفت وتسجيل الخروج بنجاح.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
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