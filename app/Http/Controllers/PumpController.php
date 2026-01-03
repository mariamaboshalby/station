<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    /**
     * Update the pump name
     */
    public function updateName(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $pump = Pump::findOrFail($id);
        
        $pump->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('tanks.report', $pump->tank_id)
            ->with('success', '✅ تم تحديث اسم الطلمبة بنجاح');
    }

    /**
     * Show the form for editing the pump
     */
    public function edit($id)
    {
        $pump = Pump::with(['tank', 'nozzles'])->findOrFail($id);
        return view('pumps.edit', compact('pump'));
    }

    /**
     * Update the pump
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $pump = Pump::findOrFail($id);
        
        $pump->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('tanks.report', $pump->tank_id)
            ->with('success', '✅ تم تحديث الطلمبة بنجاح');
    }
}
