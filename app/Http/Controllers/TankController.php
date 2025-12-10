<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function index()
    {
        $tanks = Tank::all();
        return view('tanks.index', compact('tanks'));
    }

    public function report($id)
    {
        // عرض تقرير تانك محدد
        $tank = Tank::with(['fuel', 'pumps.nozzles'])->findOrFail($id);
        return view('tanks.report-detail', compact('tank'));
    }

    public function create()
    {
        $fuels = Fuel::all(); // هات أنواع الوقود
        return view('tanks.create', compact('fuels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tank_name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:1',
            'pump_count' => 'required|integer|min:1',
            'nozzles_per_pump' => 'required|integer|min:1',
            'fuel_id' => 'required|exists:fuels,id',
        ]);

        // 1- انشاء التانك
        $tank = Tank::create([
            'name' => $request->tank_name,
            'capacity' => $request->capacity,
            'fuel_id' => $request->fuel_id,
        ]);

        // 2- pumps + nozzles
        for ($i = 1; $i <= $request->pump_count; $i++) {
            $pump = Pump::create([
                'tank_id' => $tank->id,
                'name' => "طلمبه $i",
            ]);

            for ($j = 1; $j <= $request->nozzles_per_pump; $j++) {
                Nozzle::create([
                    'pump_id' => $pump->id,
                    'name' => "مسدس $j",
                ]);
            }
        }

        return redirect()->route('tanks.index')->with('success', 'تم إنشاء التانك والطلمبات والمسدسات بنجاح ✅');
    }

    public function edit($id)
    {
        $tank = Tank::findOrFail($id);
        return view('tanks.edit', compact('tank'));
    }

    public function updateAll(Request $request, $id)
    {
        $tank = Tank::with('fuel')->findOrFail($id);

        $validated = $request->validate([
            'current_level' => 'required|numeric|min:0',
            'price_per_liter' => 'required|numeric|min:0',
            'price_for_owner' => 'required|numeric|min:0',
        ]);

        // ✅ تحديث التانك
        $tank->update(['current_level' => $validated['current_level']]);

        // ✅ تحديث أسعار الوقود المرتبط
        $tank->fuel->update([
            'price_per_liter' => $validated['price_per_liter'],
            'price_for_owner' => $validated['price_for_owner'],
        ]);

        return redirect()->route('tanks.index')->with('success', 'تم التحديث بنجاح ✅');
    }

    public function addCapacityForm($id)
    {
        $tank = Tank::findOrFail($id);
        return view('tanks.add-capacity', compact('tank'));
    }

    public function addCapacity(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cost_per_liter' => 'nullable|numeric|min:0',
            'deduct_from_treasury' => 'nullable|in:on,off',
        ]);

        $tank = Tank::with('fuel')->findOrFail($id);

        if ($tank->current_level + $request->amount > $tank->capacity) {
            return redirect()->back()->with('error', '⚠️ الكمية أكبر من السعة الكلية للتانك.');
        }

        // 1. حساب المصروف وإضافته للخزنة إذا لزم الأمر
        if ($request->has('deduct_from_treasury') && $request->cost_per_liter > 0) {
            $totalCost = $request->amount * $request->cost_per_liter;
            
            TreasuryTransaction::create([
                'user_id' => auth()->id(), // المستخدم الحالي
                'type' => 'expense',
                'category' => 'شراء وقود (تفريغ تانك)',
                'amount' => $totalCost,
                'transaction_date' => now(),
                'description' => "تفريغ حمولة {$request->amount} لتر في {$tank->name} (سعر اللتر {$request->cost_per_liter})",
            ]);
        }

        // 2. تحديث التانك
        $tank->current_level += $request->amount;
        $tank->save();
        
        $msg = '✅ تم إضافة الكمية للتانك بنجاح.';
        if($request->has('deduct_from_treasury') && $request->cost_per_liter > 0) {
            $msg .= ' وتم تسجيل المصروف في الخزنة.';
        }

        return redirect()->route('tanks.index')->with('success', $msg);
    }

    // ✅ دالة الحذف
    public function destroy($id)
    {
        $tank = Tank::findOrFail($id);

        // حذف الطلمبات والمسدسات المرتبطة به
        foreach ($tank->pumps as $pump) {
            $pump->nozzles()->delete();
            $pump->delete();
        }

        // حذف التانك نفسه
        $tank->delete();

        return redirect()->route('tanks.index')->with('success', '🗑️ تم حذف التانك وكل متعلقاته بنجاح.');
    }
}
