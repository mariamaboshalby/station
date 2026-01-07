<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use App\Models\PumpInventory;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Daily Summary Report (as requested - similar to handwritten ledger)
     */
    public function dailySummary(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // Get pump inventories for the specific date (for opening balance and sales)
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle'])
            ->where('inventory_date', $date)
            ->get();
        
        // Get inventory purchases for the specific date from Expense table
        $inventoryPurchases = Expense::where('expense_date', $date)
            ->where('category', 'purchasing')
            ->get();
        
        // Get transactions for the specific date (for actual sales)
        $transactions = \App\Models\Transaction::with(['pump.tank.fuel'])
            ->whereDate('created_at', $date)
            ->get();
        
        // Initialize data arrays
        $fuelData = [
            'solarData' => ['opening_balance' => 0, 'received' => 0, 'sales' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine92Data' => ['opening_balance' => 0, 'received' => 0, 'sales' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine80Data' => ['opening_balance' => 0, 'received' => 0, 'sales' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine95Data' => ['opening_balance' => 0, 'received' => 0, 'sales' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'oilsData' => ['opening_balance' => 0, 'received' => 0, 'sales' => 0, 'dispensed' => 0, 'actual_balance' => 0],
        ];
        
        // Get previous day's closing readings as opening balance
        $previousDate = Carbon::createFromFormat('Y-m-d', $date)->subDay()->toDateString();
        $previousInventories = PumpInventory::with(['pump.tank.fuel'])
            ->where('inventory_date', $previousDate)
            ->get();

        \Log::info('Previous Date: ' . $previousDate);
        \Log::info('Previous Inventories Count: ' . $previousInventories->count());

        // If no previous day's data, use current day's opening readings as opening balance
        if ($previousInventories->count() == 0) {
            \Log::info('No previous day data found, using current day opening readings');
            foreach ($pumpInventories as $inventory) {
                $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
                $openingReading = $inventory->opening_reading ?? 0;
                \Log::info('Using current opening reading for fuel: ' . $fuelName . ' with opening: ' . $openingReading);
                
                if (strpos($fuelName, 'سولار') !== false) {
                    $fuelData['solarData']['opening_balance'] += $openingReading;
                } elseif (strpos($fuelName, 'بنزين 92') !== false) {
                    $fuelData['benzine92Data']['opening_balance'] += $openingReading;
                } elseif (strpos($fuelName, 'بنزين 80') !== false) {
                    $fuelData['benzine80Data']['opening_balance'] += $openingReading;
                } elseif (strpos($fuelName, 'بنزين 95') !== false) {
                    $fuelData['benzine95Data']['opening_balance'] += $openingReading;
                } elseif (strpos($fuelName, 'زيوت') !== false) {
                    $fuelData['oilsData']['opening_balance'] += $openingReading;
                }
            }
        } else {
            \Log::info('Previous Inventories Data: ' . json_encode($previousInventories));
            // Calculate opening balance from previous day's closing readings
            foreach ($previousInventories as $inventory) {
                $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
                $closingReading = $inventory->closing_reading ?? 0;
                \Log::info('Processing inventory for fuel: ' . $fuelName . ' with closing reading: ' . $closingReading);
                
                if (strpos($fuelName, 'سولار') !== false) {
                    $fuelData['solarData']['opening_balance'] += $closingReading;
                } elseif (strpos($fuelName, 'بنزين 92') !== false) {
                    $fuelData['benzine92Data']['opening_balance'] += $closingReading;
                } elseif (strpos($fuelName, 'بنزين 80') !== false) {
                    $fuelData['benzine80Data']['opening_balance'] += $closingReading;
                } elseif (strpos($fuelName, 'بنزين 95') !== false) {
                    $fuelData['benzine95Data']['opening_balance'] += $closingReading;
                } elseif (strpos($fuelName, 'زيوت') !== false) {
                    $fuelData['oilsData']['opening_balance'] += $closingReading;
                }
            }
        }

        \Log::info('Fuel Data after opening balance calculation: ' . json_encode($fuelData));
        
        // Process current day's inventories for sales and dispensed amounts
        foreach ($pumpInventories as $inventory) {
            $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
            
            // Sales = closing reading (end of day reading)
            $sales = $inventory->closing_reading ?? 0;
            // Dispensed = closing - opening (actual fuel dispensed)
            $dispensed = ($inventory->closing_reading ?? 0) - ($inventory->opening_reading ?? 0);
            
            // Map fuel names to data keys
            if (strpos($fuelName, 'سولار') !== false) {
                $fuelData['solarData']['sales'] += $sales;
                $fuelData['solarData']['dispensed'] += $dispensed;
                $fuelData['solarData']['actual_balance'] = $sales; // Actual balance = closing reading
            } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                $fuelData['benzine92Data']['sales'] += $sales;
                $fuelData['benzine92Data']['dispensed'] += $dispensed;
                $fuelData['benzine92Data']['actual_balance'] = $sales;
            } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                $fuelData['benzine80Data']['sales'] += $sales;
                $fuelData['benzine80Data']['dispensed'] += $dispensed;
                $fuelData['benzine80Data']['actual_balance'] = $sales;
            } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                $fuelData['benzine95Data']['sales'] += $sales;
                $fuelData['benzine95Data']['dispensed'] += $dispensed;
                $fuelData['benzine95Data']['actual_balance'] = $sales;
            } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                $fuelData['oilsData']['sales'] += $sales;
                $fuelData['oilsData']['dispensed'] += $dispensed;
                $fuelData['oilsData']['actual_balance'] = $sales;
            }
        }
        
        // Add purchases from expense table (tank capacity additions)
        $processingDebug = [];
        foreach ($inventoryPurchases as $expense) {
            // Extract amount and fuel type from description
            $description = $expense->description ?? '';
            $amount = $expense->amount ?? 0;
            $costPerLiter = 0;
            $liters = 0;
            
            // Parse description to extract liters and fuel type
            $liters = 0;
            $tankId = 0;
            
            // Pattern 1: "تفريغ 1000 لتر في تانك 1 × 5.00 ج.م" or "تفريغ 1000 لتر في 1 × 5.00 ج.م"
            if (preg_match('/تفريغ\s+([\d.]+)\s+لتر\s+في\s+(?:تانك\s+)?(\d+)/i', $description, $matches)) {
                $liters = floatval($matches[1]);
                $tankId = intval($matches[2]);
            }
            // Pattern 2: "1000 لتر تانك 1"
            elseif (preg_match('/([\d.]+)\s+لتر\s+تانك\s+(\d+)/i', $description, $matches)) {
                $liters = floatval($matches[1]);
                $tankId = intval($matches[2]);
            }
            // Pattern 3: "tank 1 1000 liters" (English)
            elseif (preg_match('/tank\s+(\d+)\s+([\d.]+)\s+liters?/i', $description, $matches)) {
                $tankId = intval($matches[1]);
                $liters = floatval($matches[2]);
            }
            // Pattern 4: Just extract numbers if no tank found
            elseif (preg_match('/([\d.]+)\s+لتر/i', $description, $matches)) {
                $liters = floatval($matches[1]);
                // Try to find tank number from the description
                if (preg_match('/تانك\s+(\d+)/i', $description, $tankMatches)) {
                    $tankId = intval($tankMatches[1]);
                }
                // Also try to find "في [رقم]" as tank number
                elseif (preg_match('/في\s+(\d+)/i', $description, $tankMatches)) {
                    $tankId = intval($tankMatches[1]);
                }
            }
            
            // Only proceed if we found a tank
            if ($tankId > 0) {
                // Get tank info to determine fuel type
                $tank = \App\Models\Tank::with('fuel')->find($tankId);
                if ($tank) {
                    $fuelName = $tank->fuel->name ?? 'غير معروف';
                    
                    // Add to processing debug
                    $willAddTo = 'Unknown';
                    if (strpos($fuelName, 'سولار') !== false) {
                        $willAddTo = 'Solar';
                    } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                        $willAddTo = 'Benzine 92';
                    } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                        $willAddTo = 'Benzine 80';
                    } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                        $willAddTo = 'Benzine 95';
                    } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                        $willAddTo = 'Oils';
                    }
                    
                    $processingDebug[] = [
                        'description' => $description,
                        'extracted_liters' => $liters,
                        'tank_id' => $tankId,
                        'fuel_type' => $fuelName,
                        'will_add_to' => $willAddTo
                    ];
                    
                    // Map fuel names to data keys
                    if (strpos($fuelName, 'سولار') !== false) {
                        $fuelData['solarData']['received'] += $liters;
                    } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                        $fuelData['benzine92Data']['received'] += $liters;
                    } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                        $fuelData['benzine80Data']['received'] += $liters;
                    } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                        $fuelData['benzine95Data']['received'] += $liters;
                    } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                        $fuelData['oilsData']['received'] += $liters;
                    }
                }
            } else {
                $processingDebug[] = [
                    'description' => $description,
                    'error' => 'Regex failed - no tank found'
                ];
            }
        }
        
        // Calculate totals
        $totalBalance = ($fuelData['solarData']['opening_balance'] + $fuelData['benzine92Data']['opening_balance'] + 
                        $fuelData['benzine80Data']['opening_balance'] + $fuelData['benzine95Data']['opening_balance'] + 
                        $fuelData['oilsData']['opening_balance']);
        
        $totalReceived = ($fuelData['solarData']['received'] + $fuelData['benzine92Data']['received'] + 
                         $fuelData['benzine80Data']['received'] + $fuelData['benzine95Data']['received'] + 
                         $fuelData['oilsData']['received']);
        
        $totalDispensed = ($fuelData['solarData']['dispensed'] + $fuelData['benzine92Data']['dispensed'] + 
                          $fuelData['benzine80Data']['dispensed'] + $fuelData['benzine95Data']['dispensed'] + 
                          $fuelData['oilsData']['dispensed']);
        
        $finalBalance = $totalBalance + $totalReceived - $totalDispensed;
        
        // Calculate actual balance from current day's closing readings
        $totalActualBalance = ($fuelData['solarData']['actual_balance'] + $fuelData['benzine92Data']['actual_balance'] + 
                              $fuelData['benzine80Data']['actual_balance'] + $fuelData['benzine95Data']['actual_balance'] + 
                              $fuelData['oilsData']['actual_balance']);
        
        $difference = $totalActualBalance - $finalBalance;
        
        // Get invoice numbers from expenses
        $invoiceNumbers = $inventoryPurchases->pluck('invoice_number')->filter()->unique();
        $invoiceNumber = $invoiceNumbers->isNotEmpty() ? $invoiceNumbers->first() : 'INV-' . date('Ymd', strtotime($date));
        
        return view('inventory.daily-summary', array_merge($fuelData, [
            'date' => $date,
            'actualBalance' => $totalActualBalance,
            'difference' => $difference,
            'invoiceNumber' => $invoiceNumber,
            'dispensedInvoiceNumber' => 'DISP-' . date('Ymd', strtotime($date)),
            'notes' => '',
            'pumpInventoriesCount' => $pumpInventories->count(),
            'inventoryPurchasesCount' => $inventoryPurchases->count(),
            'transactionsCount' => $transactions->count(),
            'transactions' => $transactions,
            'processingDebug' => $processingDebug,
        ]));
    }
    
    /**
     * Helper function to get fuel type key
     */
    private function getFuelTypeKey($fuelName)
    {
        if (strpos($fuelName, 'سولار') !== false) {
            return 'Solar';
        } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
            return 'Benzine 92';
        } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
            return 'Benzine 80';
        } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
            return 'Benzine 95';
        } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
            return 'Oils';
        }
        return 'Unknown';
    }
    
    /**
     * Monthly Summary Report - New Design (as requested)
     */
    public function monthlySummaryNew(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        
        // Check if it's a full date (Y-m-d) or just month (Y-m)
        if (strlen($month) > 7) { // If longer than Y-m format, assume it's Y-m-d
            // Extract just the Y-m part for monthly processing
            $month = substr($month, 0, 7);
        }
        
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
        
        // Get all pump inventories for the month
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle'])
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->get();
        
        // Initialize data arrays
        $fuelData = [
            'solarData' => ['balance' => 0, 'received' => 0, 'dispensed' => 0],
            'benzine92Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0],
            'benzine80Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0],
            'benzine95Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0],
            'oilsData' => ['balance' => 0, 'received' => 0, 'dispensed' => 0],
        ];
        
        // Group by fuel type and calculate summary
        foreach ($pumpInventories as $inventory) {
            $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
            
            // Calculate balance from opening_reading
            $balance = $inventory->opening_reading ?? 0;
            $dispensed = $inventory->sales ?? 0;
            $received = 0; // This might need to be calculated from other sources
            
            // Map fuel names to data keys
            if (strpos($fuelName, 'سولار') !== false) {
                $fuelData['solarData']['balance'] += $balance;
                $fuelData['solarData']['received'] += $received;
                $fuelData['solarData']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                $fuelData['benzine92Data']['balance'] += $balance;
                $fuelData['benzine92Data']['received'] += $received;
                $fuelData['benzine92Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                $fuelData['benzine80Data']['balance'] += $balance;
                $fuelData['benzine80Data']['received'] += $received;
                $fuelData['benzine80Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                $fuelData['benzine95Data']['balance'] += $balance;
                $fuelData['benzine95Data']['received'] += $received;
                $fuelData['benzine95Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                $fuelData['oilsData']['balance'] += $balance;
                $fuelData['oilsData']['received'] += $received;
                $fuelData['oilsData']['dispensed'] += $dispensed;
            }
        }
        
        return view('inventory.monthly.summary', array_merge($fuelData, [
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
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
