<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Tank;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use Mpdf\Mpdf;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $inventories = Inventory::with(['tank.fuel', 'user'])
            ->where('type', $type)
            ->whereDate('inventory_date', $date)
            ->get();

        $tanks = Tank::with('fuel')->get();

        return view('inventory.index', compact('inventories', 'tanks', 'type', 'date'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', date('Y-m-d'));

        $tanks = Tank::with('fuel')->get();

        return view('inventory.create', compact('tanks', 'type', 'date'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_date' => 'required|date',
            'type' => 'required|in:daily,monthly',
            'supplier' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'inventories' => 'required|array',
            'inventories.*.tank_id' => 'required|exists:tanks,id',
            'inventories.*.purchases' => 'nullable|numeric|min:0',
            'inventories.*.actual_balance' => 'required|numeric|min:0',
            'inventories.*.notes' => 'nullable|string',
        ]);

        foreach ($validated['inventories'] as $item) {
            $tank = Tank::with('fuel')->findOrFail($item['tank_id']);
            $openingBalance = $tank->current_level;
            $purchases = $item['purchases'] ?? 0;
            $sales = $tank->liters_drawn;
            $closingBalance = $openingBalance + $purchases - $sales;
            $actualBalance = $item['actual_balance'];
            $difference = $actualBalance - $closingBalance;

            Inventory::create([
                'inventory_date' => $validated['inventory_date'],
                'type' => $validated['type'],
                'supplier' => $validated['supplier'] ?? null,
                'invoice_number' => $validated['invoice_number'] ?? null,
                'invoice_date' => $validated['invoice_date'] ?? null,
                'tank_id' => $item['tank_id'],
                'fuel_type' => $tank->fuel->name,
                'opening_balance' => $openingBalance,
                'purchases' => $purchases,
                'sales' => $sales,
                'closing_balance' => $closingBalance,
                'actual_balance' => $actualBalance,
                'difference' => $difference,
                'notes' => $item['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.index', ['type' => $validated['type'], 'date' => $validated['inventory_date']])
            ->with('success', 'تم حفظ الجرد بنجاح ✅');
    }

    public function report(Request $request)
    {
        $type = $request->input('type', 'monthly');
        if ($type === 'monthly') {
            // Auto-set to first day to last day of current month
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        } else {
            // Daily or custom range
            $startDate = $request->input('start_date', Carbon::now()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->toDateString());
        }
        $inventories = Inventory::with(['tank.fuel', 'user'])
            ->where('type', $type)
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->orderBy('inventory_date', 'desc')
            ->get();

        return view('inventory.report', compact('inventories', 'type', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'monthly');
        if ($type === 'monthly') {
            // Auto-set to first day to last day of current month
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        } else {
            // Daily or custom range
            $startDate = $request->input('start_date', Carbon::now()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->toDateString());
        }
        $exportType = $request->input('export_type', 'excel');

        $inventories = Inventory::with(['tank.fuel', 'user'])
            ->where('type', $type)
            ->whereBetween('inventory_date', [$startDate, $endDate])
            ->orderBy('inventory_date', 'desc')
            ->get();

        if ($exportType == 'pdf') {
            $data = [
                'inventories' => $inventories,
                'type' => $type,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'P', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
            $html = view('inventory.report-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="inventory_report_' . $startDate . '_' . $endDate . '.pdf"');
        } else {
            return Excel::download(new InventoryExport($inventories), 'inventory_' . $startDate . '_' . $endDate . '.xlsx');
        }
    }
}
