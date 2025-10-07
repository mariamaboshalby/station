<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'pump_id',
        'name',
        'liters_drawn',
        'total_price',
        'amount_paid',
        'rest',
    ];

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }
}
