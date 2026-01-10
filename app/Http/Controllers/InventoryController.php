<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use App\Models\PumpInventory;
use App\Models\Inventory;
use App\Models\ActualBalance;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Display inventory main page
     */
    public function index()
    {
        return view('inventory.index');
    }

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
        
        // Get inventory purchases for the specific date from Expense table with tank relationship
        $inventoryPurchases = Expense::with('tank.fuel')
            ->where('expense_date', $date)
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

                
        // Process current day's inventories for sales and dispensed amounts
        foreach ($pumpInventories as $inventory) {
            $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
            
            // Sales = closing reading (end of day reading)
            $sales = $inventory->closing_reading ?? 0;
            // Dispensed = closing - opening (actual fuel dispensed)
            $dispensed = ($inventory->closing_reading ?? 0) - ($inventory->opening_reading ?? 0);
            
            // Check if there's a manual actual balance for this fuel type
            $fuelId = $inventory->pump->tank->fuel->id ?? null;
            $manualActualBalance = $fuelId ? $this->getManualActualBalance($date, $fuelId) : null;
            
            // Map fuel names to data keys
            if (strpos($fuelName, 'سولار') !== false) {
                $fuelData['solarData']['sales'] += $sales;
                $fuelData['solarData']['dispensed'] += $dispensed;
                // Use manual actual balance if available, otherwise use closing reading
                $fuelData['solarData']['actual_balance'] = $manualActualBalance ?? $sales;
            } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                $fuelData['benzine92Data']['sales'] += $sales;
                $fuelData['benzine92Data']['dispensed'] += $dispensed;
                $fuelData['benzine92Data']['actual_balance'] = $manualActualBalance ?? $sales;
            } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                $fuelData['benzine80Data']['sales'] += $sales;
                $fuelData['benzine80Data']['dispensed'] += $dispensed;
                $fuelData['benzine80Data']['actual_balance'] = $manualActualBalance ?? $sales;
            } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                $fuelData['benzine95Data']['sales'] += $sales;
                $fuelData['benzine95Data']['dispensed'] += $dispensed;
                $fuelData['benzine95Data']['actual_balance'] = $manualActualBalance ?? $sales;
            } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                $fuelData['oilsData']['sales'] += $sales;
                $fuelData['oilsData']['dispensed'] += $dispensed;
                $fuelData['oilsData']['actual_balance'] = $manualActualBalance ?? $sales;
            }
        }
        
        // Process inventory purchases
        foreach ($inventoryPurchases as $expense) {
            if ($expense->tank && $expense->tank->fuel) {
                $fuelName = $expense->tank->fuel->name;
                
                // Extract liters from description
                $liters = 0;
                $description = $expense->description ?? '';
                if (preg_match('/([\d.]+)\s+لتر/i', $description, $matches)) {
                    $liters = floatval($matches[1]);
                }
                
                // Map fuel names to data keys using the tank's fuel type
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
            } else {
                $processingDebug[] = [
                    'description' => $expense->description ?? 'N/A',
                    'tank_id' => $expense->tank_id,
                    'error' => 'No tank relationship found'
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
        
        // Get all pump inventories for month
        $pumpInventories = PumpInventory::with(['pump.tank.fuel', 'nozzle'])
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->get();
        
        // Get inventory purchases (fuel received) from expenses for the month
        $inventoryPurchases = Expense::with('tank.fuel')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('category', 'purchasing')
            ->get();
        
        // Get manual actual balances for the month
        $actualBalances = ActualBalance::with('fuel')
            ->whereBetween('balance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('fuel_id');
        
        // Initialize data arrays
        $fuelData = [
            'solarData' => ['balance' => 0, 'received' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine92Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine80Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'benzine95Data' => ['balance' => 0, 'received' => 0, 'dispensed' => 0, 'actual_balance' => 0],
            'oilsData' => ['balance' => 0, 'received' => 0, 'dispensed' => 0, 'actual_balance' => 0],
        ];
        
        // Process received fuel from expenses
        foreach ($inventoryPurchases as $expense) {
            if ($expense->tank && $expense->tank->fuel) {
                $fuelName = $expense->tank->fuel->name;
                
                // Extract liters from description
                $liters = 0;
                $description = $expense->description ?? '';
                if (preg_match('/([\d.]+)\s+لتر/i', $description, $matches)) {
                    $liters = floatval($matches[1]);
                }
                
                // Add to received based on fuel type
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
        }
        
        // Process actual balances from manual entries
        foreach ($actualBalances as $fuelId => $balanceGroup) {
            $lastBalance = $balanceGroup->last();
            if ($lastBalance && $lastBalance->fuel) {
                $fuelName = $lastBalance->fuel->name;
                
                if (strpos($fuelName, 'سولار') !== false) {
                    $fuelData['solarData']['actual_balance'] = $lastBalance->actual_balance;
                } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                    $fuelData['benzine92Data']['actual_balance'] = $lastBalance->actual_balance;
                } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                    $fuelData['benzine80Data']['actual_balance'] = $lastBalance->actual_balance;
                } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                    $fuelData['benzine95Data']['actual_balance'] = $lastBalance->actual_balance;
                } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                    $fuelData['oilsData']['actual_balance'] = $lastBalance->actual_balance;
                }
            }
        }
        
        // Group by fuel type and calculate summary
        foreach ($pumpInventories as $inventory) {
            $fuelName = $inventory->pump->tank->fuel->name ?? 'غير معروف';
            
            // Calculate balance from opening_reading
            $balance = $inventory->opening_reading ?? 0;
            $dispensed = $inventory->sales ?? 0;
            
            // Map fuel names to data keys
            if (strpos($fuelName, 'سولار') !== false) {
                $fuelData['solarData']['balance'] += $balance;
                $fuelData['solarData']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 92') !== false || strpos($fuelName, '92') !== false) {
                $fuelData['benzine92Data']['balance'] += $balance;
                $fuelData['benzine92Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 80') !== false || strpos($fuelName, '80') !== false) {
                $fuelData['benzine80Data']['balance'] += $balance;
                $fuelData['benzine80Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'بنزين 95') !== false || strpos($fuelName, '95') !== false) {
                $fuelData['benzine95Data']['balance'] += $balance;
                $fuelData['benzine95Data']['dispensed'] += $dispensed;
            } elseif (strpos($fuelName, 'زيت') !== false || strpos($fuelName, 'زيوت') !== false) {
                $fuelData['oilsData']['balance'] += $balance;
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

    /**
     * Show form to enter actual balances manually
     */
    public function actualBalanceForm(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $fuels = Fuel::all();
        
        // Get existing actual balances for the date
        $existingBalances = ActualBalance::with('fuel')
            ->where('balance_date', $date)
            ->get()
            ->keyBy('fuel_id');
        
        return view('inventory.actual-balance-form', compact('date', 'fuels', 'existingBalances'));
    }

    /**
     * Store actual balances manually
     */
    public function actualBalanceStore(Request $request)
    {
        $request->validate([
            'balance_date' => 'required|date',
            'balances' => 'required|array',
            'balances.*.fuel_id' => 'required|exists:fuels,id',
            'balances.*.actual_balance' => 'required|numeric|min:0',
        ]);

        $balanceDate = $request->balance_date;

        foreach ($request->balances as $balanceData) {
            ActualBalance::updateOrCreate(
                [
                    'balance_date' => $balanceDate,
                    'fuel_id' => $balanceData['fuel_id'],
                ],
                [
                    'actual_balance' => $balanceData['actual_balance'],
                    'user_id' => auth()->id,
                    'notes' => $balanceData['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('inventory.actual.balance.form', ['date' => $balanceDate])
            ->with('success', 'تم حفظ الرصيد الفعلي بنجاح ✅');
    }

    /**
     * Update dailySummary to use manual actual balances
     */
    private function getManualActualBalance($date, $fuelId)
    {
        $actualBalance = ActualBalance::where('balance_date', $date)
            ->where('fuel_id', $fuelId)
            ->first();
        
        return $actualBalance ? $actualBalance->actual_balance : null;
    }
}
