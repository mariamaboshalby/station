<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'pump_id',
        'client_id',
        'credit_liters',
        'cash_liters',
        'total_amount',
        'image',
        'notes',
    ];


    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }


    public function pump()
    {
        return $this->belongsTo(Pump::class, 'pump_id');
    }


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
