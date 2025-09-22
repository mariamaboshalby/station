<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    protected $fillable = ['name', 'price_per_liter'];

    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }
}
