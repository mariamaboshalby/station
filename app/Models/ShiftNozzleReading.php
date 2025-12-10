<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftNozzleReading extends Model
{
    protected $fillable = [
        'shift_id',
        'nozzle_id',
        'start_reading',
        'end_reading',
        'liters_dispensed',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class);
    }
}
