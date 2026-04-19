<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'event_time',
        'status_id', 'created_by', 'published_at', 'closed_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ============================================
    // العلاقات
    // ============================================

    // الفعالية لها حالة
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    // مين أنشأها
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // حجوزات الفعالية
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // سجل التغييرات
    public function logs()
    {
        return $this->hasMany(EventLog::class);
    }

    // إشعارات الفعالية
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ============================================
    // دوال فحص الحالة
    // ============================================

    public function isDraft(): bool
    {
        return $this->status->name === Status::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status->name === Status::PUBLISHED;
    }

    public function isBookable(): bool
    {
        return $this->status->name === Status::PUBLISHED;
    }

    // ============================================
    // إحصائيات
    // ============================================

    // كم حجز مؤكد
    public function reservedSeatsCount(): int
    {
        return $this->reservations()
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    // كم حضر فعلاً
    public function checkedInCount(): int
    {
        return $this->reservations()
            ->where('status', 'checked_in')
            ->count();
    }

    // كم مقعد متاح
    public function availableSeatsCount(): int
    {
        return 945 - $this->reservedSeatsCount();
    }

    public function occupancyRate(): float
    {
        return round(($this->reservedSeatsCount() / 945) * 100, 1);
    }
}
