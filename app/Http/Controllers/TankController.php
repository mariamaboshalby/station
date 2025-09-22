<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function create()
    {
        $fuels = \App\Models\Fuel::all(); // هات أنواع الوقود
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
                'name' => "Pump $i",
            ]);

            for ($j = 1; $j <= $request->nozzles_per_pump; $j++) {
                Nozzle::create([
                    'pump_id' => $pump->id,
                    'name' => "Nozzle $j",
                ]);
            }
        }

        return redirect()->route('tanks.create')->with('success', 'Tank, pumps, and nozzles created successfully!');
    }

}
