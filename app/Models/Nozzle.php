<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nozzle extends Model
{
    protected $fillable = ['pump_id', 'name', 'meter_reading'];

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }


    // إضافة علاقة للوصول للتانك مباشرة
    public function tank()
    {
        return $this->hasOneThrough(
            Tank::class,
            Pump::class,
            'id',        // المفتاح في جدول pumps
            'id',        // المفتاح في جدول tanks
            'pump_id',   // المفتاح في جدول nozzles
            'tank_id'    // المفتاح في جدول pumps
        );
    }

    public function readings()
    {
        return $this->hasMany(NozzleReading::class);
    }
}
