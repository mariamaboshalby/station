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
        $permissions = Permission::all(); // 👈 نجيب كل الصلاحيات
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
                'phone'    => $validated['phone'], // ✅ بدل email بالفون
                'password' => Hash::make($validated['password']),
            ]);

            // ✅ ربط الصلاحيات
            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('name', $validated['permissions'])->get();
                $user->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
