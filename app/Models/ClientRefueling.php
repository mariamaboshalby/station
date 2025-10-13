<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRefueling extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'shift_id',
        'transaction_id',
        'liters',
        'price_per_liter',
        'total_amount',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
