<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nozzle extends Model
{
    protected $fillable = ['pump_id', 'name'];

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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
}
