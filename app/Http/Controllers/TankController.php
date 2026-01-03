<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use App\Models\TreasuryTransaction;
use App\Models\Expense;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TankReportExport;

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
            'fuel_id' => 'required|exists:fuels,id',
        ]);

        // 1- انشاء التانك
        $tank = Tank::create([
            'name' => $request->tank_name,
            'capacity' => $request->capacity,
            'fuel_id' => $request->fuel_id,
        ]);

        // 2- انشاء الطلمبات فقط بدون مسدسات
        for ($i = 1; $i <= $request->pump_count; $i++) {
            Pump::create([
                'tank_id' => $tank->id,
                'name' => "طلمبة $i",
            ]);
        }

        return redirect()->route('tanks.index')->with('success', 'تم إنشاء التانك والطلمبات والمسدسات بنجاح ✅');
    }

    public function edit($id)
    {
        $tank = Tank::findOrFail($id);
        return view('tanks.edit', compact('tank'));
    }

    public function update(Request $request, $id)
    {
        $tank = Tank::with('fuel')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'current_level' => 'required|numeric|min:0',
            'price_per_liter' => 'required|numeric|min:0',
            'price_for_owner' => 'required|numeric|min:0',
        ]);

        // ✅ تحديث اسم التانك
        $tank->update(['name' => $validated['name']]);

        // ✅ تحديث السعة الحالية
        $tank->update(['current_level' => $validated['current_level']]);

        // ✅ تحديث أسعار الوقود المرتبط
        $tank->fuel->update([
            'price_per_liter' => $validated['price_per_liter'],
            'price_for_owner' => $validated['price_for_owner'],
        ]);

        return redirect()->route('tanks.index')->with('success', 'تم تحديث بيانات التانك بنجاح ✅');
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
        $tank = Tank::with('fuel')->findOrFail($id);
        return view('tanks.add-capacity', compact('tank'));
    }

    public function addCapacity(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cost_per_liter' => 'nullable|numeric|min:0',
            'invoice_number' => 'required|string',
            'deduct_from_treasury' => 'nullable|in:on,off',
        ]);

        $tank = Tank::with('fuel')->findOrFail($id);

        if ($tank->current_level + $request->amount > $tank->capacity) {
            return redirect()->back()->with('error', '⚠️ الكمية أكبر من السعة الكلية للتانك.');
        }

        // 1. حساب المصروف وإضافته للخزنة (إذا تم تفعيل الخيار وإدخال السعر)
        $expenseRecorded = false;
        
        if ($request->has('deduct_from_treasury')) {
            $costPerLiter = $request->cost_per_liter ?? 0;
            
            if ($costPerLiter > 0) {
                $totalCost = $request->amount * $costPerLiter;
                
                // Create Expense record
                Expense::create([
                    'user_id' => auth()->id(),
                    'category' => 'purchasing',
                    'amount' => $totalCost,
                    'description' => "تفريغ {$request->amount} لتر في {$tank->name} × {$costPerLiter} ج.م",
                    'expense_date' => now()->toDateString(),
                    'invoice_number' => $request->invoice_number,
                ]);
                
                // Also create Treasury transaction for treasury tracking
                TreasuryTransaction::create([
                    'user_id' => auth()->id(),
                    'type' => 'expense',
                    'category' => 'شراء وقود (تفريغ تانك)',
                    'amount' => $totalCost,
                    'transaction_date' => now(),
                    'description' => "تفريغ {$request->amount} لتر في {$tank->name} × {$costPerLiter} ج.م",
                ]);
                
                $expenseRecorded = true;
            }
        }

        // 2. تحديث التانك
        $tank->current_level += $request->amount;
        $tank->save();
        
        // 3. رسالة النجاح
        if ($expenseRecorded) {
            $msg = '✅ تم إضافة ' . number_format($request->amount, 2) . ' لتر للتانك وتسجيل المصروف في الخزنة.';
        } else {
            if ($request->has('deduct_from_treasury') && !$request->cost_per_liter) {
                $msg = '✅ تم إضافة الكمية للتانك. ⚠️ لم يتم تسجيل مصروف (السعر غير محدد).';
            } else {
                $msg = '✅ تم إضافة الكمية للتانك بنجاح.';
            }
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

    // إضافة مسدس جديد لطلمبة محددة
    public function storeNozzle(Request $request, $pumpId)
    {
        $pump = Pump::findOrFail($pumpId);
        
        $request->validate([
            'nozzle_name' => 'required|string|max:255',
            'meter_reading' => 'required|numeric|min:0',
        ]);

        Nozzle::create([
            'pump_id' => $pump->id,
            'name' => $request->nozzle_name,
            'meter_reading' => $request->meter_reading,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المسدس بنجاح ✅');
    }
    
    public function reportPdf($id)
    {
        $tank = Tank::with(['fuel', 'pumps.nozzles'])->findOrFail($id);
        
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'P', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $html = view('tanks.report-pdf', compact('tank'))->render();
        $mpdf->WriteHTML($html);
        
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="tank_report_' . $tank->id . '_' . now()->format('Y-m-d') . '.pdf"');
    }

    public function reportExcel($id)
    {
        $tank = Tank::with(['fuel', 'pumps.nozzles'])->findOrFail($id);
        
        return Excel::download(new TankReportExport($tank), 'tank_report_' . $tank->id . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    // حذف مسدس
    public function destroyNozzle($id)
    {
        $nozzle = Nozzle::findOrFail($id);
        $nozzle->delete();
        
        return redirect()->back()->with('success', 'تم حذف المسدس بنجاح 🗑️');
    }
}
