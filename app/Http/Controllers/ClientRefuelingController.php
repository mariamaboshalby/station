<?php

namespace App\Http\Controllers;

use App\Models\ClientRefueling;

class ClientRefuelingController extends Controller
{
    public function destroy($id)
    {
        $refueling = ClientRefueling::findOrFail($id);
        $refueling->delete();

        return back();
    }
}
