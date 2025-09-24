<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

       public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع العمليات (transactions)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // عرض البداية بتوقيت القاهرة بصيغة جاهزة
    public function getStartTimeCairoAttribute()
    {
        if (! $this->start_time) return null;
        return $this->start_time->setTimezone('Africa/Cairo')->format('Y-m-d H:i:s');
    }

    // عرض النهاية بتوقيت القاهرة بصيغة جاهزة
    public function getEndTimeCairoAttribute()
    {
        if (! $this->end_time) return null;
        return $this->end_time->setTimezone('Africa/Cairo')->format('Y-m-d H:i:s');
    }
}
