<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'pump_id',            // 🔹 إضافة رقم الطلمبة المرتبطة بالشيفت
        'operation_type',
        'meter_reading',
        'meter_match',
        'meter_image',
        'cash_sales',
        'credit_sales',
        'end_meter_reading',
        'end_meter_image',
        'notes',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // العلاقة مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع العمليات
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // 🔹 العلاقة مع الطلمبة
    public function pump(): BelongsTo
    {
        return $this->belongsTo(Pump::class);
    }
}
