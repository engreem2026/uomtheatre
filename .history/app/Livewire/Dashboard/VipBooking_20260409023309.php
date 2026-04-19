<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Event;
use App\Models\Seat;
use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('حجز مقاعد الوفود')]
class VipBooking extends Component
{
    public int $eventId;
    public string $guestName = '';
    public string $guestPhone = '';
    public int $selectedSeatId = 0;
    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(int $id)
    {
        $this->eventId = $id;
    }

    // ============================================
    // اختيار مقعد للحجز
    // ============================================
    public function selectSeat(int $seatId)
    {
        $this->selectedSeatId = $seatId;
        $this->guestName = '';
        $this->guestPhone = '';
        $this->errorMessage = '';
    }

    // ============================================
    // حجز المقعد للضيف
    // ============================================
    public function bookSeat()
    {
        $this->validate([
            'guestName'  => 'required|string|max:255',
            'guestPhone' => 'required|string|min:10',
        ], [
            'guestName.required'  => 'اسم الضيف مطلوب',
            'guestPhone.required' => 'رقم الجوال مطلوب',
            'guestPhone.min'      => 'رقم الجوال غير صحيح',
        ]);

        $seat = Seat::findOrFail($this->selectedSeatId);

        // تحقق: هل المقعد محجوز؟
        $existing = Reservation::where('event_id', $this->eventId)
            ->where('seat_id', $this->selectedSeatId)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            $this->errorMessage = 'هذا المقعد محجوز بالفعل لـ ' . ($existing->guest_name ?? 'ضيف');
            return;
        }

        // احجز المقعد
        Reservation::create([
            'user_id'     => session('user_id'),
            'event_id'    => $this->eventId,
            'seat_id'     => $this->selectedSeatId,
            'status'      => 'confirmed',
            'type'        => 'vip_guest',
            'guest_name'  => $this->guestName,
            'guest_phone' => $this->guestPhone,
        ]);

        $this->successMessage = 'تم حجز المقعد ' . $seat->label . ' للضيف ' . $this->guestName;
        $this->reset(['guestName', 'guestPhone', 'selectedSeatId', 'errorMessage']);
        $this->dispatch('close-modal');
    }

    // ============================================
    // إلغاء حجز ضيف
    // ============================================
    public function cancelBooking(int $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $name = $reservation->guest_name;
        $reservation->update(['status' => 'cancelled']);
        $this->successMessage = 'تم إلغاء حجز ' . $name;
    }

    // ============================================
    // توليد رابط واتساب
    // ============================================
    public function getWhatsAppLink(int $reservationId): string
    {
        $res = Reservation::with(['event', 'seat.section'])->findOrFail($reservationId);
        $event = $res->event;
        $seat = $res->seat;

        // البحث عن الجار (المقعد المجاور)
        $neighborLeft = Reservation::with('seat')
            ->where('event_id', $this->eventId)
            ->where('status', 'confirmed')
            ->whereHas('seat', function ($q) use ($seat) {
                $q->where('section_id', $seat->section_id)
                  ->where('row_number', $seat->row_number)
                  ->where('seat_number', $seat->seat_number - 1);
            })->first();

        $neighborRight = Reservation::with('seat')
            ->where('event_id', $this->eventId)
            ->where('status', 'confirmed')
            ->whereHas('seat', function ($q) use ($seat) {
                $q->where('section_id', $seat->section_id)
                  ->where('row_number', $seat->row_number)
                  ->where('seat_number', $seat->seat_number + 1);
            })->first();

        // بناء الرسالة
        $message = "🎭 *دعوة حضور — " . $event->title . "*\n\n";
        $message .= "السلام عليكم " . $res->guest_name . "\n\n";
        $message .= "يسعدنا دعوتكم لحضور:\n";
        $message .= " *" . $event->title . "*\n";
        if ($event->description) $message .= $event->description . "\n";
        $message .= "\n التاريخ: " . $event->event_date->format('Y-m-d') . "\n";
        if ($event->event_time) $message .= " الوقت: " . $event->event_time . "\n";
        $message .= "📍 المكان: مسرح جامعة الموصل\n\n";
        $message .= "💺 *معلومات المقعد:*\n";
        $message .= "• القسم: " . $seat->section->name . "\n";
        $message .= "• الصف: " . $seat->row_number . "\n";
        $message .= "• رقم المقعد: " . $seat->seat_number . "\n";
        $message .= "• الرمز: " . $seat->label . "\n\n";

        // الجيران
        if ($neighborLeft || $neighborRight) {
            $message .= " بجانبك:\n";
            if ($neighborLeft) $message .= "• على يمينك: " . ($neighborLeft->guest_name ?? 'ضيف') . "\n";
            if ($neighborRight) $message .= "• على يسارك: " . ($neighborRight->guest_name ?? 'ضيف') . "\n";
            $message .= "\n";
        }

        $message .= " رمز الدخول (QR): " . $res->qr_code . "\n\n";
        $message .= "نتشرف بحضوركم 🌟\n";
        $message .= "جامعة الموصل";

        // تنظيف رقم الجوال
        $phone = preg_replace('/[^0-9]/', '', $res->guest_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '964' . substr($phone, 1); // تحويل 07xx → 9647xx
        }

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public function render()
    {
        $allowed = ['super_admin', 'event_manager'];
        if (!in_array(session('role_name'), $allowed)) {
            return redirect()->route('dashboard');
        }

        $event = Event::with('status')->findOrFail($this->eventId);

        // مقاعد الوفود (صف 10 في أقسام A, B, C)
        $vipSeats = Seat::with('section')
            ->where('is_vip_reserved', true)
            ->orderBy('section_id')
            ->orderBy('seat_number')
            ->get();

        // الحجوزات الحالية لهذه الفعالية
        $bookings = Reservation::with(['seat.section'])
            ->where('event_id', $this->eventId)
            ->where('type', 'vip_guest')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->keyBy('seat_id');

        return view('livewire.dashboard.vip-booking', [
            'event'    => $event,
            'vipSeats' => $vipSeats,
            'bookings' => $bookings,
        ]);
    }
}
