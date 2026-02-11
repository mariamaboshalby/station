<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFuelPrice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'fuel_id', 'price'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }
}
