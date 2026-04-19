<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Event;
use App\Models\Seat;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    // حجوزاتي
    // GET /api/my-reservations
    public function myReservations(Request $request)
    {
        $reservations = Reservation::with(['event.status', 'seat.section'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($res) {
                return [
                    'id'         => $res->id,
                    'event'      => $res->event->title,
                    'event_date' => $res->event->event_date,
                    'section'    => $res->seat->section->name,
                    'is_vip'     => $res->seat->section->is_vip,
                    'label'      => $res->seat->label,
                    'status'     => $res->status,
                    'type'       => $res->type,
                    'qr_code'    => $res->qr_code,
                    'created_at' => $res->created_at,
                ];
            });

        return response()->json(['reservations' => $reservations]);
    }

    // حجز مقعد
    // POST /api/reservations
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'seat_id'  => 'required|exists:seats,id',
        ]);

        $event = Event::findOrFail($request->event_id);
        $seat = Seat::findOrFail($request->seat_id);

        // تحقق: الفعالية منشورة؟
        if (!$event->isPublished()) {
            return response()->json(['message' => 'الفعالية غير متاحة للحجز'], 422);
        }

        // تحقق: المقعد مقعد وفود؟
        if ($seat->is_vip_reserved) {
            return response()->json(['message' => 'هذا المقعد مخصص للوفود'], 422);
        }

        // تحقق: المستخدم ما عنده حجز ثاني في نفس الفعالية
        $existingReservation = Reservation::where('user_id', $request->user()->id)
            ->where('event_id', $request->event_id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingReservation) {
            return response()->json(['message' => 'عندك حجز مسبق في هذه الفعالية'], 422);
        }

        // حجز بـ Transaction + Lock
        try {
            $reservation = DB::transaction(function () use ($request, $seat, $event) {
                // قفل المقعد
                $lockedSeat = Seat::lockForUpdate()->find($seat->id);

                // تحقق مرة ثانية: هل المقعد لسا متاح؟
                if ($lockedSeat->isReservedForEvent($event->id)) {
                    throw new \Exception('المقعد محجوز');
                }

                // احجز!
                return Reservation::create([
                    'user_id'  => $request->user()->id,
                    'event_id' => $event->id,
                    'seat_id'  => $seat->id,
                    'status'   => 'confirmed',
                    'type'     => 'regular',
                ]);
            });

            return response()->json([
                'message'     => 'تم الحجز بنجاح',
                'reservation' => $reservation->ticketData(),
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'المقعد محجوز، اختر مقعد آخر'], 422);
        }
    }

    // عرض التذكرة
    // GET /api/reservations/{id}/ticket
    public function ticket($id, Request $request)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json(['ticket' => $reservation->ticketData()]);
    }

    // إلغاء حجز
    // PATCH /api/reservations/{id}/cancel
    public function cancel($id, Request $request)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'confirmed')
            ->firstOrFail();

        $reservation->cancel();

        return response()->json(['message' => 'تم إلغاء الحجز بنجاح']);
    }
}
