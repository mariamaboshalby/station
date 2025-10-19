<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fuel;

class FuelSeeder extends Seeder
{
    public function run()
    {
        $fuels = [
            ['name' => 'بنزين 80'],
            ['name' => 'بنزين 92'],
            ['name' => 'بنزين 95'],
            ['name' => 'سولار'],
        ];


        foreach ($fuels as $fuel) {
            Fuel::create($fuel);
        }
    }
}
