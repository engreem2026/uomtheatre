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
```

### 🔍 شرح الهيكل:
```
routes/api.php
│
├── 🌍 روابط عامة (أي شخص يقدر يوصلها)
│   ├── POST /api/register        ← تسجيل حساب
│   ├── POST /api/login           ← تسجيل دخول
│   ├── GET  /api/events          ← عرض الفعاليات المنشورة
│   └── GET  /api/events/1        ← تفاصيل فعالية
│
├── 🔒 روابط تحتاج تسجيل دخول (auth:sanctum)
│   ├── POST /api/logout          ← تسجيل خروج
│   ├── GET  /api/me              ← بياناتي
│   ├── GET  /api/events/1/seat-map ← خريطة المقاعد
│   ├── POST /api/reservations    ← حجز مقعد
│   ├── GET  /api/my-reservations ← حجوزاتي
│   └── GET  /api/notifications   ← إشعاراتي
│
└── 🛡️ روابط الإدارة (admin middleware)
    ├── GET  /api/admin/users     ← إدارة الموظفين
    ├── POST /api/admin/events    ← إنشاء فعالية
    ├── PATCH /api/admin/events/1/status ← تغيير الحالة
    ├── POST /api/admin/check-in  ← مسح QR
    └── GET  /api/admin/dashboard ← الإحصائيات
```

---

## ✅ احفظي الملفات الثلاثة:
```
1. app/Http/Middleware/AdminMiddleware.php    ✅
2. bootstrap/app.php                         ✅
3. routes/api.php                            ✅