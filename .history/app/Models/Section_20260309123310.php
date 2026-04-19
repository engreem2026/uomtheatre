<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'is_vip', 'total_seats', 'total_rows'];

    // تحويل تلقائي
    protected $casts = [
        'is_vip' => 'boolean',
    ];

    // ============================================
    // العلاقات
    // ============================================

    // القسم فيه عدة مقاعد
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    // ============================================
    // دوال مساعدة
    // ============================================

    // كم مقعد متاح في فعالية معينة؟
    public function availableSeatsForEvent($eventId)
    {
        $reservedCount = $this->seats()
            ->whereHas('reservations', function ($query) use ($eventId) {
                $query->where('event_id', $eventId)
                      ->where('status', '!=', 'cancelled');
            })->count();

        return $this->total_seats - $reservedCount;
    }
}