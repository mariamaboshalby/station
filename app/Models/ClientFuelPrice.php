<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFuelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'fuel_id',
        'price_per_liter',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }
}
