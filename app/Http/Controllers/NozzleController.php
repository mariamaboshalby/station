<?php

namespace App\Http\Controllers;

use App\Models\Nozzle;
use Illuminate\Http\Request;

class NozzleController extends Controller
{
    /**
     * Update the meter reading for a specific nozzle
     */
    public function updateMeter(Request $request, $id)
    {
        $request->validate([
            'meter_reading' => 'required|numeric|min:0',
        ]);

        $nozzle = Nozzle::findOrFail($id);
        
        $nozzle->update([
            'meter_reading' => $request->meter_reading,
        ]);

        // Get tank_id through pump relationship
        $tankId = $nozzle->pump->tank_id;

        return redirect()
            ->route('tanks.report', $tankId)
            ->with('success', '✅ تم تحديث قراءة العداد بنجاح');
    }
}
