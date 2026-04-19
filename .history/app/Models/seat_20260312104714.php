<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['section_id', 'row_number', 'seat_number', 'label'];

    // المقعد تابع لقسم
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // المقعد عنده عدة حجوزات (في فعاليات مختلفة)
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // هل المقعد محجوز في فعالية معينة؟
    public function isReservedForEvent($eventId): bool
    {
        return $this->reservations()
            ->where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    // وش حالة المقعد في فعالية معينة؟
    public function statusForEvent($eventId): string
    {
        $reservation = $this->reservations()
            ->where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$reservation) {
            return 'available';     // متاح 🟢
        }
        if ($reservation->type === 'vip_guest') {
            return 'vip_guest';     // وفود ⬜
        }
        if ($reservation->status === 'checked_in') {
            return 'checked_in';    // حضر ✅
        }
        return 'reserved';          // محجوز 🔴
    }
}
