<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'phone',
        'password',
        'fuel_price',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // العلاقة مع الشيفتات
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    // أسعار الوقود الخاصة بالمستخدم
    public function fuelPrices()
    {
        return $this->hasMany(UserFuelPrice::class);
    }
}
