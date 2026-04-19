<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Event;
use App\Models\Seat;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
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

    public function mount(int $id) { $this->eventId = $id; }

    public function selectSeat(int $seatId)
    {
        $this->selectedSeatId = $seatId;
        $this->guestName = ''; $this->guestPhone = ''; $this->errorMessage = '';
    }

    public function bookSeat()
    {
        $this->validate([
            'guestName'=>'required|string|max:255','guestPhone'=>'required|string|min:10',
        ],['guestName.required'=>'اسم الضيف مطلوب','guestPhone.required'=>'رقم الجوال مطلوب','guestPhone.min'=>'رقم الجوال غير صحيح']);

        $seat = Seat::findOrFail($this->selectedSeatId);
        $existing = Reservation::where('event_id',$this->eventId)->where('seat_id',$this->selectedSeatId)->where('status','!=','cancelled')->first();
        if ($existing) { $this->errorMessage = 'هذا المقعد محجوز لـ ' . ($existing->guest_name ?? 'ضيف'); return; }

        Reservation::create([
            'user_id'=>Auth::id(),'event_id'=>$this->eventId,'seat_id'=>$this->selectedSeatId,
            'status'=>'confirmed','type'=>'vip_guest','guest_name'=>$this->guestName,'guest_phone'=>$this->guestPhone,
        ]);
        $this->successMessage = 'تم حجز المقعد ' . $seat->label . ' للضيف ' . $this->guestName;
        $this->reset(['guestName','guestPhone','selectedSeatId','errorMessage']);
        $this->dispatch('close-modal');
    }

    public function cancelBooking(int $reservationId)
    {
        $res = Reservation::findOrFail($reservationId);
        $name = $res->guest_name;
        $res->update(['status' => 'cancelled']);
        $this->successMessage = 'تم إلغاء حجز ' . $name;
    }

    public function getWhatsAppLink(int $reservationId): string
    {
        $res = Reservation::with(['event','seat.section'])->findOrFail($reservationId);
        $event = $res->event; $seat = $res->seat;

        $neighborLeft = Reservation::with('seat')->where('event_id',$this->eventId)->where('status','confirmed')
            ->whereHas('seat', fn($q) => $q->where('section_id',$seat->section_id)->where('row_number',$seat->row_number)->where('seat_number',$seat->seat_number - 1))->first();
        $neighborRight = Reservation::with('seat')->where('event_id',$this->eventId)->where('status','confirmed')
            ->whereHas('seat', fn($q) => $q->where('section_id',$seat->section_id)->where('row_number',$seat->row_number)->where('seat_number',$seat->seat_number + 1))->first();

        $msg = "🎭 *دعوة حضور — {$event->title}*\n\n";
        $msg .= "السلام عليكم {$res->guest_name}\n\n";
        $msg .= "يسعدنا دعوتكم لحضور:\n📌 *{$event->title}*\n";
        if ($event->description) $msg .= "{$event->description}\n";
        $msg .= "\n📅 التاريخ: " . $event->event_date->format('Y-m-d') . "\n";
        if ($event->event_time) $msg .= "🕐 الوقت: {$event->event_time}\n";
        $msg .= "📍 المكان: مسرح جامعة الموصل\n\n";
        $msg .= "💺 *معلومات المقعد:*\n• القسم: {$seat->section->name}\n• الصف: {$seat->row_number}\n• رقم المقعد: {$seat->seat_number}\n• الرمز: {$seat->label}\n\n";
        if ($neighborLeft || $neighborRight) {
            $msg .= "👥 *بجانبك:*\n";
            if ($neighborLeft) $msg .= "• على يمينك: " . ($neighborLeft->guest_name ?? 'ضيف') . "\n";
            if ($neighborRight) $msg .= "• على يسارك: " . ($neighborRight->guest_name ?? 'ضيف') . "\n";
            $msg .= "\n";
        }
        $msg .= "🎫 رمز الدخول: {$res->qr_code}\n\nنتشرف بحضوركم 🌟\nجامعة الموصل";

        $phone = preg_replace('/[^0-9]/','',$res->guest_phone);
        if (str_starts_with($phone,'0')) $phone = '964' . substr($phone,1);
        return 'https://wa.me/' . $phone . '?text=' . urlencode($msg);
    }

    public function render()
    {
        if (!in_array(Auth::user()->role->name, ['super_admin','event_manager'])) return redirect()->route('dashboard');

        $event = Event::with('status')->findOrFail($this->eventId);
        $vipSeats = Seat::with('section')->where('is_vip_reserved',true)->orderBy('section_id')->orderBy('seat_number')->get();
        $bookings = Reservation::with(['seat.section'])->where('event_id',$this->eventId)->where('type','vip_guest')->where('status','!=','cancelled')->get()->keyBy('seat_id');

        return view('livewire.dashboard.vip-booking', ['event'=>$event,'vipSeats'=>$vipSeats,'bookings'=>$bookings]);
    }
}
