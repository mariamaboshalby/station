<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'shift_id',
        'pump_id',
        'client_id',
        'credit_liters',
        'credit_amount',
        'cash_liters',
        'cash_amount',
        'total_amount',
        'tank_level_after',
        'image',
        'notes',
    ];

    // العلاقة مع الشيفت
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // العلاقة مع الطرمبة
    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    // العلاقة مع العميل (للبيع الآجل)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // تحديث الإجمالي تلقائيًا قبل الحفظ
    protected static function booted()
    {
        static::saving(function ($transaction) {
            $transaction->total_amount = $transaction->credit_amount + $transaction->cash_amount;
        });
    }

    // دوال مساعدة لحساب الإجماليات
    public static function totals()
    {
        return [
            'credit_total' => static::sum('credit_amount'),
            'cash_total'   => static::sum('cash_amount'),
            'grand_total'  => static::sum('total_amount'),
        ];
    }

    // سكوبات لتسهيل الاستعلام
    public function scopeCredit($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    public function scopeCash($query)
    {
        return $query->where('cash_amount', '>', 0);
    }
}
