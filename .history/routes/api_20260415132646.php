<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\SeatMapController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;

// ============================================
// روابط عامة (بدون تسجيل دخول)
// ============================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// الفعاليات المنشورة (للجمهور)
Route::get('/events', [EventController::class, 'publicIndex']);
Route::get('/events/{id}', [EventController::class, 'show']);

// ============================================
// روابط تحتاج تسجيل دخول
// ============================================
Route::middleware('auth:sanctum')->group(function () {

    // المصادقة
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // خريطة المقاعد
    Route::get('/events/{eventId}/seat-map', [SeatMapController::class, 'getSeatMap']);

    // الحجوزات
    Route::get('/my-reservations', [ReservationController::class, 'myReservations']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}/ticket', [ReservationController::class, 'ticket']);
    Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    // الإشعارات
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // ============================================
    // روابط الإدارة (تحتاج صلاحية)
    // ============================================
    Route::middleware('admin')->prefix('admin')->group(function () {

        // إدارة المستخدمين
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::get('/roles', [UserController::class, 'roles']);

        // إدارة الفعاليات
        Route::get('/events', [EventController::class, 'index']);
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{id}', [EventController::class, 'update']);
        Route::patch('/events/{id}/status', [EventController::class, 'changeStatus']);
        Route::post('/events/{id}/vip-seats', [EventController::class, 'reserveVip']);
        Route::get('/events/{id}/logs', [EventController::class, 'logs']);

        // تسجيل الحضور
        Route::post('/check-in', [CheckInController::class, 'checkIn']);

        // لوحة المؤشرات
        Route::get('/events/{id}/dashboard', [DashboardController::class, 'eventDashboard']);
        Route::get('/dashboard', [DashboardController::class, 'overview']);
    });
});
// الفعاليات المنشورة (للتطبيق — بدون تسجيل دخول)
Route::get('/events', function () {
    $publishedStatus = \App\Models\Status::where('name', 'published')->first();
    if (!$publishedStatus) return response()->json([]);

    $events = \App\Models\Event::where('status_id', $publishedStatus->id)
        ->orderBy('event_date', 'asc')
        ->get()
        ->map(function ($event) {
            $totalSeats = \App\Models\Seat::where('is_vip_reserved', false)->count();
            $booked = \App\Models\Reservation::where('event_id', $event->id)
                ->where('status', '!=', 'cancelled')
                ->where('type', '!=', 'vip_guest')
                ->count();

            return [
                'id'          => $event->id,
                'title'       => $event->title,
                'description' => $event->description,
                'event_date'  => $event->event_date->format('Y-m-d'),
                'event_time'  => $event->event_time,
                'total_seats' => $totalSeats,
                'booked'      => $booked,
                'available'   => $totalSeats - $booked,
            ];
        });

    return response()->json($events);
});
