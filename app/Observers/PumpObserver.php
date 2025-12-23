<?php

namespace App\Observers;

use App\Models\Pump;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PumpObserver
{
    /**
     * Handle the Pump "created" event.
     */
    public function created(Pump $pump): void
    {
        // 🟢 إنشاء صلاحية الاستخدام الخاصة بالطلمبة الجديدة
        $permissionName = "use_pump_{$pump->id}";
        
        $permission = Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web'
        ]);

        // 🟢 إعطاء الصلاحية للأدمن تلقائياً
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }
    }

    /**
     * Handle the Pump "deleted" event.
     */
    public function deleted(Pump $pump): void
    {
        // 🗑️ حذف الصلاحية عند حذف الطلمبة
        $permissionName = "use_pump_{$pump->id}";
        Permission::where('name', $permissionName)->delete();
    }
}
