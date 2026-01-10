<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActualBalance extends Model
{
    protected $fillable = [
        'balance_date',
        'fuel_id',
        'actual_balance',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'balance_date' => 'date',
        'actual_balance' => 'decimal:2',
    ];

    // العلاقة مع نوع الوقود
    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
