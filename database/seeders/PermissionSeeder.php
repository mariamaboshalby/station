<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pump;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder
{
    public function run()
    {
        $modulesPermissions = [
            'users' => ['add user', 'show users'],
            'shifts' => ['open shift', 'show shifts', 'close shift', 'show report'],
            'transactions' => ['add transaction', 'show transaction'],
            'tanks' => ['add tank', 'edit tank', 'show tanks'],
            'clients' => ['add client', 'edit client', 'show clients'],
            'dashboard' => ['view dashboard'],
        ];

        // 🟢 إنشاء الصلاحيات العامة
        foreach ($modulesPermissions as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }

        // 🟢 إنشاء الأدوار
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // ------------------------------
        // 🟢 جلب كل الطلمبات من الداتابيز
        // ------------------------------
        $pumps = Pump::all();
        $this->command->info("✅ تم جلب {$pumps->count()} طلمبة من الداتابيز");

        // 🟢 إنشاء صلاحيات الاستخدام لكل طلمبة
        foreach ($pumps as $pump) {
            $permissionName = "use_pump_{$pump->id}";

            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            $this->command->info("   - تم إنشاء صلاحية: {$permissionName} للطلمبة: {$pump->name} (ID: {$pump->id})");
        }

        // 🟢 ربط جميع الصلاحيات بالـ admin role
        $adminRole->syncPermissions(Permission::all());

        // ------------------------------
        // 🟢 إنشاء يوزر الأدمن الافتراضي
        // ------------------------------
        $adminUser = User::updateOrCreate(
            ['phone' => '01111111111'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        $adminUser->assignRole($adminRole);
        $generalPermissionsCount = count($modulesPermissions, COUNT_RECURSIVE) - count($modulesPermissions);
    }
}