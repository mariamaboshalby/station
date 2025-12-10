<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'inventory_date',
        'type',
        'supplier',
        'invoice_number',
        'invoice_date',
        'tank_id',
        'fuel_type',
        'opening_balance',
        'purchases',
        'sales',
        'closing_balance',
        'actual_balance',
        'difference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'invoice_date' => 'date',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
