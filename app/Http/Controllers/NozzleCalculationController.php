<?php

namespace App\Http\Controllers;

use App\Models\Nozzle;
use App\Models\NozzleReading;
use App\Models\Pump;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class NozzleCalculationController extends Controller
{
    /**
     * عرض صفحة حساب اللترات
     */
    public function index()
    {
        $openShifts = Shift::whereNull('end_time')
            ->with('user')
            ->latest()
            ->get();

        $lastCalculations = NozzleReading::with(['nozzle.pump', 'shift'])
            ->latest()
            ->take(20)
            ->get()
            ->groupBy('shift_id');

        return view('nozzles.calculate', compact('openShifts', 'lastCalculations'));
    }

    /**
     * تنفيذ حساب اللترات
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
        ]);

        try {
            // تشغيل الـ Command
            Artisan::call('nozzles:calculate', [
                'shift_id' => $validated['shift_id']
            ]);

            $output = Artisan::output();

            return redirect()->route('nozzles.calculate.index')
                ->with('success', 'تم حساب اللترات بنجاح ✅')
                ->with('output', $output);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تقرير القراءات لشيفت معين
     */
    public function report($shiftId)
    {
        $shift = Shift::with('user')->findOrFail($shiftId);
        
        $readings = NozzleReading::where('shift_id', $shiftId)
            ->with(['nozzle.pump.tank.fuel'])
            ->get();

        $totalLiters = $readings->sum('liters_dispensed');
        
        $groupedByPump = $readings->groupBy(function($reading) {
            return $reading->nozzle->pump->id;
        });

        return view('nozzles.report', compact('shift', 'readings', 'totalLiters', 'groupedByPump'));
    }
}
