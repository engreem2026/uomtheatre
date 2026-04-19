<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Index;
use App\Livewire\Dashboard\Users;
use App\Livewire\Dashboard\Events;
use App\Livewire\Dashboard\CheckIn;
use App\Livewire\Dashboard\Stats;

// ============================================
// تسجيل الدخول (بدون حماية)
// ============================================
Route::get('/login', Login::class)->name('login');

// تسجيل الخروج
Route::get('/logout', function () {
    session()->flush();
    return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
})->name('dashboard.logout');

// ============================================
// لوحة التحكم (محمية بـ Middleware)
// ============================================
Route::middleware('admin.web')->group(function () {
    Route::get('/dashboard',          Index::class)->name('dashboard');
    Route::get('/dashboard/users',    Users::class)->name('dashboard.users');
    Route::get('/dashboard/events',   Events::class)->name('dashboard.events');
    Route::get('/dashboard/check-in', CheckIn::class)->name('dashboard.checkin');
    Route::get('/dashboard/stats',    Stats::class)->name('dashboard.stats');
});

// الصفحة الرئيسية تحوّل للداشبورد
Route::get('/', function () {
    return redirect('/login');
});
