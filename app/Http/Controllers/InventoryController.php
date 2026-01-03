<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use App\Models\PumpInventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Monthly Inventory Index
     */
    public function monthlyIndex(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
        
        return view('inventory.monthly.index', compact('month', 'startDate', 'endDate'));
    }
    
    /**
     * Monthly Detailed Report (from pump inventories)
     */
    public function monthlyDetailed(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
        
        // Get all pump inventories for the month
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle'])
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->orderBy('inventory_date')
            ->orderBy('pump_id')
            ->get();
        
        // Group by pump and date
        $groupedInventories = $pumpInventories->groupBy(function($item) {
            return $item->inventory_date . '_' . $item->pump_id;
        });
        
        return view('inventory.monthly.detailed', compact('pumpInventories', 'groupedInventories', 'month', 'startDate', 'endDate'));
    }
    
    /**
     * Monthly Summary Report (aggregated from pump inventories)
     */
    public function monthlySummary(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
        
        // Get all pump inventories for the month
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle'])
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->get();
        
        // Aggregate by pump
        $summary = $pumpInventories->groupBy('pump_id')->map(function($inventories) {
            $pump = $inventories->first()->pump;
            $totalSales = $inventories->sum('sales');
            $totalDispensed = $inventories->sum('liters_dispensed');
            $totalRevenue = $inventories->sum(function($inv) {
                return $inv->sales * ($inv->pump->tank->fuel->price_for_client ?? 0);
            });
            
            return [
                'pump' => $pump,
                'total_sales' => $totalSales,
                'total_dispensed' => $totalDispensed,
                'total_revenue' => $totalRevenue,
                'days_count' => $inventories->count(),
                'avg_daily_sales' => $totalSales / max($inventories->count(), 1),
            ];
        });
        
        // Grand totals
        $grandTotalSales = $summary->sum('total_sales');
        $grandTotalDispensed = $summary->sum('total_dispensed');
        $grandTotalRevenue = $summary->sum('total_revenue');
        
        return view('inventory.monthly.summary', compact(
            'summary', 
            'grandTotalSales', 
            'grandTotalDispensed', 
            'grandTotalRevenue', 
            'month', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Pump inventory index
     */
    public function pumpIndex(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // Get all pumps with their nozzles
        $pumps = Pump::with(['nozzles', 'tank.fuel'])->get();
        
        return view('inventory.pump-index', compact('pumps', 'date'));
    }

    /**
     * Pump inventory create form
     */
    public function pumpCreate(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // Get all pumps with their nozzles
        $pumps = Pump::with(['nozzles', 'tank.fuel'])->get();
        
        return view('inventory.pump-create', compact('pumps', 'date'));
    }

    /**
     * Store pump inventory
     */
    public function pumpStore(Request $request)
    {
        $validated = $request->validate([
            'inventory_date' => 'required|date',
            'nozzles' => 'required|array',
            'nozzles.*.opening_reading' => 'required|numeric|min:0',
            'nozzles.*.closing_reading' => 'required|numeric|min:0',
        ]);

        foreach ($validated['nozzles'] as $nozzleId => $nozzleData) {
            $nozzle = Nozzle::with(['pump.tank.fuel'])->findOrFail($nozzleId);
            
            $openingReading = $nozzleData['opening_reading'];
            $closingReading = $nozzleData['closing_reading'];
            
            // Calculate sales as closing reading (this will be start of next day)
            $sales = $closingReading;
            // Calculate cash liters = End of Shift - Start of Shift
            $litersDispensed = $closingReading - $openingReading;
            
            // Store pump inventory record
            PumpInventory::create([
                'inventory_date' => $validated['inventory_date'],
                'pump_id' => $nozzle->pump_id,
                'nozzle_id' => $nozzle->id,
                'tank_id' => $nozzle->pump->tank_id,
                'fuel_type' => $nozzle->pump->tank->fuel->name,
                'opening_reading' => $openingReading,
                'closing_reading' => $closingReading,
                'liters_dispensed' => $litersDispensed,
                'sales' => $sales,
                'notes' => $nozzleData['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);
            
            // Update nozzle meter reading
            $nozzle->update(['meter_reading' => $closingReading]);
        }

        return redirect()->route('inventory.pump.index', ['date' => $validated['inventory_date']])
            ->with('success', 'تم حفظ جرد الطلمبات بنجاح ✅');
    }

    /**
     * Pump inventory report
     */
    public function pumpReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle', 'user'])
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->orderBy('inventory_date', 'desc')
            ->orderBy('pump_id')
            ->get();

        return view('inventory.pump-report', compact('pumpInventories', 'startDate', 'endDate'));
    }
}
