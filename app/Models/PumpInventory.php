<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumpInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_date',
        'pump_id',
        'nozzle_id',
        'tank_id',
        'fuel_type',
        'opening_reading',
        'closing_reading',
        'liters_dispensed',
        'sales',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'opening_reading' => 'decimal:2',
        'closing_reading' => 'decimal:2',
        'liters_dispensed' => 'decimal:2',
        'sales' => 'decimal:2',
    ];

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
