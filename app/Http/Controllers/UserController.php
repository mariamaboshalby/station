<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * عرض قائمة جميع المستخدمين
     */
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * عرض نموذج إنشاء مستخدم جديد
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $fuels = \App\Models\Fuel::all();
        return view('users.create', compact('roles', 'permissions', 'fuels'));
    }

    /**
     * تخزين مستخدم جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|digits:11|unique:users,phone',
            'password'     => 'required|min:4|confirmed',
            'fuel_prices'  => 'nullable|array',
            'fuel_prices.*'=> 'nullable|numeric|min:0',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'       => $validated['name'],
                'phone'      => $validated['phone'],
                'password'   => Hash::make($validated['password']),
            ]);

            // حفظ أسعار الوقود الخاصة
            if (!empty($validated['fuel_prices'])) {
                foreach ($validated['fuel_prices'] as $fuelId => $price) {
                    if ($price !== null) {
                        \App\Models\UserFuelPrice::create([
                            'user_id' => $user->id,
                            'fuel_id' => $fuelId,
                            'price'   => $price,
                        ]);
                    }
                }
            }

            // ربط الصلاحيات
            $permissionsToSync = [];
            if (!empty($validated['permissions'])) {
                $permissionsToSync = Permission::whereIn('name', $validated['permissions'])->pluck('id')->toArray();
            }
            $user->syncPermissions($permissionsToSync);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض بيانات مستخدم واحد
     */
    public function show($id)
    {
        $user = User::with(['roles', 'permissions', 'fuelPrices'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * عرض فورم تعديل مستخدم
     */
    public function edit($id)
    {
        $user = User::with('permissions', 'fuelPrices')->findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();
        $fuels = \App\Models\Fuel::all();

        return view('users.edit', compact('user', 'roles', 'permissions', 'fuels'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // إذا كانت كلمة المرور فارغة، نقوم بإزالتها من الطلب لتجنب مشاكل التحقق
        if (empty($request->password)) {
            $request->request->remove('password');
            $request->request->remove('password_confirmation');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|digits:11|unique:users,phone,' . $user->id,
            'password'     => 'nullable|min:4|confirmed',
            'fuel_prices'  => 'nullable|array',
            'fuel_prices.*'=> 'nullable|numeric|min:0',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'string',
        ]);

        DB::beginTransaction();

        try {
            // تحديث البيانات الأساسية
            $updateData = [
                'name'       => $validated['name'],
                'phone'      => $validated['phone'],
            ];

            // تحديث كلمة المرور لو موجودة
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // تحديث أسعار الوقود الخاصة (حذف القديم وإضافة الجديد)
            $user->fuelPrices()->delete();
            if (!empty($validated['fuel_prices'])) {
                foreach ($validated['fuel_prices'] as $fuelId => $price) {
                    if ($price !== null) {
                        \App\Models\UserFuelPrice::create([
                            'user_id' => $user->id,
                            'fuel_id' => $fuelId,
                            'price'   => $price,
                        ]);
                    }
                }
            }

            // تحديث الصلاحيات
            $permissionsToSync = [];
            if (!empty($validated['permissions'])) {
                $permissionsToSync = Permission::whereIn('name', $validated['permissions'])->pluck('id')->toArray();
            }
            $user->syncPermissions($permissionsToSync);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    /**
     * حذف المستخدم
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}