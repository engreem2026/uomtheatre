<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ============================================
    // تسجيل حساب جديد
    // POST /api/register
    // ============================================
    public function register(Request $request)
    {
        // 1. تحقق من البيانات
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string',
        ]);

        // 2. أنشئ المستخدم
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'phone'    => $request->phone,
            'role_id'  => 6, // مستخدم عادي
        ]);

        // 3. أنشئ توكن (بطاقة دخول)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. رجّع البيانات
        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // ============================================
    // تسجيل دخول
    // POST /api/login
    // ============================================
    public function login(Request $request)
    {
        // 1. تحقق من البيانات
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 2. ابحث عن المستخدم بالبريد
        $user = User::where('email', $request->email)->first();

        // 3. تحقق: المستخدم موجود + كلمة المرور صحيحة؟
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد أو كلمة المرور غير صحيحة'],
            ]);
        }

        // 4. تحقق: الحساب فعال؟
        if (!$user->is_active) {
            return response()->json([
                'message' => 'حسابك معطّل. تواصل مع الإدارة',
            ], 403);
        }

        // 5. أنشئ توكن
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. رجّع البيانات مع الدور
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user'    => $user->load('role'),
            'token'   => $token,
        ]);
    }

    // ============================================
    // تسجيل خروج
    // POST /api/logout
    // ============================================
    public function logout(Request $request)
    {
        // احذف التوكن الحالي
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

   
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('role'),
        ]);
    }
}
