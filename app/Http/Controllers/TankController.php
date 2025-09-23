<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\Tank;
use App\Models\Pump;
use App\Models\Nozzle;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function index()
{
    $tanks = Tank::all();
    return view('tanks.index', compact('tanks'));
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

        return redirect()->route('tanks.index')->with('success', 'Tank, pumps, and nozzles created successfully!');
    }
        public function edit($id)
    {
        $tank = Tank::findOrFail($id);
        return view('tanks.edit', compact('tank'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'current_level' => 'required|numeric|min:0',
        ]);

        $tank = Tank::findOrFail($id);
        $tank->current_level = $request->current_level;
        $tank->save();

        return redirect()->route('tanks.index', $id)->with('success', 'تم تحديث سعة التانك بنجاح ✅');
    }

}
