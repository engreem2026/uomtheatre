<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // قائمة الموظفين (بدون المستخدمين العاديين)
    // GET /api/admin/users
    public function index()
    {
        $users = User::with('role')
            ->where('role_id', '!=', 6)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['users' => $users]);
    }

    // إنشاء موظف جديد
    // POST /api/admin/users
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'phone'    => $request->phone,
            'role_id'  => $request->role_id,
        ]);

        return response()->json([
            'message' => 'تم إنشاء الموظف بنجاح',
            'user'    => $user->load('role'),
        ], 201);
    }

    // عرض موظف واحد
    // GET /api/admin/users/{id}
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json(['user' => $user]);
    }

    // تعديل موظف
    // PUT /api/admin/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'sometimes|string|max:255',
            'email'   => 'sometimes|email|unique:users,email,' . $id,
            'phone'   => 'nullable|string',
            'role_id' => 'sometimes|exists:roles,id',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'role_id']));

        return response()->json([
            'message' => 'تم تعديل الموظف بنجاح',
            'user'    => $user->load('role'),
        ]);
    }

    // تفعيل/تعطيل حساب
    // PATCH /api/admin/users/{id}/toggle-status
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'تم تفعيل' : 'تم تعطيل';

        return response()->json([
            'message' => $status . ' حساب ' . $user->name,
            'user'    => $user,
        ]);
    }

    // قائمة الأدوار
    // GET /api/admin/roles
    public function roles()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }
}
