<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'يجب تسجيل الدخول أولاً',
            ], 401);
        }

        if (!$request->user()->isActive()) {
            return response()->json([
                'message' => 'حسابك معطّل. تواصل مع الإدارة',
            ], 403);
        }

        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        return $next($request);
    }
}
