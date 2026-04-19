
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
    Route::get('/dashboard',          fn() => view('pages.dashboard'))->name('dashboard');
    Route::get('/dashboard/users',    fn() => view('pages.users'))->name('dashboard.users');
    Route::get('/dashboard/events',   fn() => view('pages.events'))->name('dashboard.events');
    Route::get('/dashboard/check-in', fn() => view('pages.checkin'))->name('dashboard.checkin');
    Route::get('/dashboard/stats',    fn() => view('pages.stats'))->name('dashboard.stats');
});

Route::get('/', fn() => redirect('/login'));
