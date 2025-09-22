<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['shift_id', 'nozzle_id', 'liters_dispensed', 'total_price'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class);
    }
}
