<?php

use Illuminate\Support\Facades\Route;

// تسجيل الدخول
Route::get('/login', function () {
    if (session('user_id')) return redirect()->route('dashboard');
    return view('pages.login');
})->name('login');

// تسجيل الخروج
Route::get('/logout', function () {
    session()->flush();
    return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
})->name('dashboard.logout');

// لوحة التحكم
Route::middleware('admin.web')->group(function () {
    Route::get('/dashboard',            fn() => view('pages.dashboard'))->name('dashboard');
    Route::get('/dashboard/users',      fn() => view('pages.users'))->name('dashboard.users');
    Route::get('/dashboard/staff',      fn() => view('pages.staff'))->name('dashboard.staff');
    Route::get('/dashboard/events',     fn() => view('pages.events'))->name('dashboard.events');
    Route::get('/dashboard/check-in',   fn() => view('pages.checkin'))->name('dashboard.checkin');
    Route::get('/dashboard/stats',      fn() => view('pages.stats'))->name('dashboard.stats');

    // حجز مقاعد الوفود لفعالية معينة
    Route::get('/dashboard/events/{eventId}/vip-booking', function ($eventId) {
        return view('pages.vip-booking', ['eventId' => $eventId]);
    })->name('dashboard.vip-booking');
});
Route::get('/test-vip', function () {
    // علّم مقاعد الصف 10 في أقسام A, B, C كمقاعد وفود
    $updated = \App\Models\Seat::whereIn('section_id', [1, 2, 3])
        ->where('row_number', 10)
        ->update(['is_vip_reserved' => true]);

    $count = \App\Models\Seat::where('is_vip_reserved', true)->count();
    return "تم تعليم $updated مقعد كوفود ✅ | المجموع: $count";
});
