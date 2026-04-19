<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminWebMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (session('role_name') === 'user') {
            session()->flush();
            return redirect()->route('login')->with('error', 'لوحة التحكم مخصصة للموظفين. استخدم التطبيق للحجز.');
        }

        return $next($request);
    }
}
