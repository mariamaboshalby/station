<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * تخزين مستخدم جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|digits:11|unique:users,phone',
            'password'     => 'required|min:8|confirmed',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'phone'    => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);

            // ✅ ربط الصلاحيات (حتى لو مفيش، هيعمل sync بـ array فاضي)
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
        $user = User::with(['roles', 'permissions'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * عرض فورم تعديل مستخدم
     */
    public function edit($id)
    {
        $user = User::with('permissions')->findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|digits:11|unique:users,phone,' . $user->id,
            'password'     => 'nullable|min:8|confirmed',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'string',
        ]);

        DB::beginTransaction();

        try {
            // تحديث البيانات الأساسية
            $updateData = [
                'name'  => $validated['name'],
                'phone' => $validated['phone'],
            ];

            // تحديث كلمة المرور لو موجودة
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // ✅ تحديث الصلاحيات دائماً (حتى لو array فاضي)
            $permissionsToSync = [];
            if (!empty($validated['permissions'])) {
                $permissionsToSync = Permission::whereIn('name', $validated['permissions'])->pluck('id')->toArray();
            }
            
            // هنا بنعمل sync دائماً، لو مفيش permissions هيمسح الكل
            $user->syncPermissions($permissionsToSync);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
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