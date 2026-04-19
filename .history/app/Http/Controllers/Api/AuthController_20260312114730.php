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

    // ============================================
    // بياناتي
    // GET /api/me
    // ============================================
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('role'),
        ]);
    }
}
```

### 🔍 شرح كل دالة:

**`register` — إنشاء حساب:**
```
1. validate = تحقق إن البيانات صحيحة
   ├── name: مطلوب + نص + حد أقصى 255
   ├── email: مطلوب + بريد صحيح + ما يتكرر
   ├── password: مطلوب + 6 أحرف على الأقل
   └── phone: اختياري

2. User::create = أنشئ المستخدم في قاعدة البيانات

3. createToken = أعطه بطاقة دخول (توكن)

4. response()->json = رجّع الرد كـ JSON
```

**`login` — تسجيل دخول:**
```
1. ابحث عن المستخدم بالبريد
2. تحقق من كلمة المرور (Hash::check)
3. تحقق إن الحساب فعال (is_active)
4. أعطه توكن

إذا البريد غلط ← "البريد أو كلمة المرور غير صحيحة"
إذا الحساب معطّل ← "حسابك معطّل"
إذا كل شي صح ← يرجّع التوكن + البيانات
```

**`logout` — تسجيل خروج:**
```
يحذف التوكن ← المستخدم ما يقدر يستخدمه مرة ثانية
مثل: ترجعين بطاقة الدخول عند الباب
```

**`me` — بياناتي:**
```
يرجّع بيانات المستخدم الحالي مع دوره
$request->user() = "مين اللي مسجل دخول حالياً؟"
->load('role') = "جيب معلومات دوره أيضاً"
```

### 📊 أمثلة على الطلبات والردود:
```
طلب تسجيل حساب:
POST /api/register
{
    "name": "سارة أحمد",
    "email": "sara@uom.edu",
    "password": "123456"
}

الرد:
{
    "message": "تم إنشاء الحساب بنجاح",
    "user": {"id": 2, "name": "سارة أحمد", ...},
    "token": "1|abc123xyz..."     ← بطاقة الدخول
}
```
```
طلب تسجيل دخول:
POST /api/login
{
    "email": "sara@uom.edu",
    "password": "123456"
}

الرد (نجح):
{
    "message": "تم تسجيل الدخول بنجاح",
    "user": {"id": 2, "name": "سارة أحمد", "role": {"name": "user"}},
    "token": "2|def456abc..."
}

الرد (فشل):
{
    "message": "البريد أو كلمة المرور غير صحيحة"
}
