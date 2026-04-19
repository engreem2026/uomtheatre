<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. هل مسجل دخول؟
        if (!$request->user()) {
            return response()->json([
                'message' => 'يجب تسجيل الدخول أولاً',
            ], 401);
        }

        // 2. هل حسابه فعال؟
        if (!$request->user()->isActive()) {
            return response()->json([
                'message' => 'حسابك معطّل. تواصل مع الإدارة',
            ], 403);
        }

        // 3. هل عنده صلاحية إدارية؟
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        // كل شي تمام ← كمّل
        return $next($request);
    }
}
```

### 🔍 شرح الأكواد:
```
401 = Unauthorized (ما سجّل دخول)
403 = Forbidden (سجّل دخول بس ما عنده صلاحية)
$next($request) = "خلّيه يكمل للـ Controller"
