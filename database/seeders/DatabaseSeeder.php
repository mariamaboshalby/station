<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pump;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
     
        // 🟢 استدعاء باقي الـ Seeders
        $this->call([
            FuelSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
