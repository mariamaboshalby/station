<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tank extends Model
{
    protected $fillable = ['fuel_id', 'name', 'capacity', 'current_level','liters_drawn'];

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    public function pumps()
    {
        return $this->hasMany(Pump::class);
    }
    
}
