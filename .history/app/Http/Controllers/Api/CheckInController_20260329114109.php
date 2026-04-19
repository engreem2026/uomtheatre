<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    // مسح QR وتسجيل حضور
    // POST /api/admin/check-in
    public function checkIn(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $reservation = Reservation::with(['user', 'event', 'seat.section'])
            ->where('qr_code', $request->qr_code)
            ->first();

        // التذكرة غير موجودة
        if (!$reservation) {
            return response()->json(['message' => 'رمز QR غير صالح'], 404);
        }

        // التذكرة ملغاة
        if ($reservation->status === 'cancelled') {
            return response()->json(['message' => 'هذا الحجز ملغي'], 422);
        }

        // تم التسجيل مسبقاً
        if ($reservation->status === 'checked_in') {
            return response()->json([
                'message'    => 'تم تسجيل الحضور مسبقاً',
                'checked_at' => $reservation->checked_in_at,
            ], 422);
        }

        // سجّل الحضور
        $reservation->checkIn();

        return response()->json([
            'message' => 'تم تسجيل الحضور بنجاح',
            'data'    => [
                'name'    => $reservation->user->name,
                'event'   => $reservation->event->title,
                'section' => $reservation->seat->section->name,
                'seat'    => $reservation->seat->label,
                'type'    => $reservation->type,
            ],
        ]);
    }
}
