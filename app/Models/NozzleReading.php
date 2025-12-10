<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NozzleReading extends Model
{
    protected $fillable = [
        'nozzle_id',
        'shift_id',
        'previous_reading',
        'current_reading',
        'liters_dispensed',
        'reading_date',
    ];

    protected $casts = [
        'reading_date' => 'datetime',
    ];

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
