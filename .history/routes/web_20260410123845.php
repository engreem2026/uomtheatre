<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/login', function () {
    if (Auth::check()) return redirect()->route('dashboard');
    return view('pages.login');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
})->name('dashboard.logout');

Route::middleware('admin.web')->group(function () {
    Route::get('/dashboard',            fn() => view('pages.dashboard'))->name('dashboard');
    Route::get('/dashboard/users',      fn() => view('pages.users'))->name('dashboard.users');
    Route::get('/dashboard/staff',      fn() => view('pages.staff'))->name('dashboard.staff');
    Route::get('/dashboard/events',     fn() => view('pages.events'))->name('dashboard.events');
    Route::get('/dashboard/check-in',   fn() => view('pages.checkin'))->name('dashboard.checkin');
    Route::get('/dashboard/stats',      fn() => view('pages.stats'))->name('dashboard.stats');
    Route::get('/dashboard/events/{eventId}/vip-booking', fn($eventId) => view('pages.vip-booking', ['eventId' => $eventId]))->name('dashboard.vip-booking');
});

Route::get('/', fn() => redirect('/login'));
