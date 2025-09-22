<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    protected $fillable = ['tank_id', 'name'];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function nozzles()
    {
        return $this->hasMany(Nozzle::class);
    }
}
