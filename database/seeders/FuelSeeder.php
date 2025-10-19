<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fuel;

class FuelSeeder extends Seeder
{
    public function run()
    {
        $fuels = [
            ['name' => 'بنزين 80', 'price_per_liter' => 17.75],
            ['name' => 'بنزين 92', 'price_per_liter' => 19.25],
            ['name' => 'بنزين 95', 'price_per_liter' => 21.00],
            ['name' => 'سولار', 'price_per_liter' => 17.50],
        ];


        foreach ($fuels as $fuel) {
            Fuel::create($fuel);
        }
    }
}
