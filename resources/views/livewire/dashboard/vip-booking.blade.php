<div>

@if($successMessage)
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> {{ $successMessage }}<button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="$set('successMessage', '')"></button></div>
@endif

{{-- معلومات الفعالية --}}
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1" style="color: var(--primary-dark);">🎭 {{ $event->title }}</h5>
            <span class="text-muted"><i class="bi bi-calendar3"></i> {{ $event->event_date->format('Y-m-d') }}</span>
            @if($event->event_time) <span class="text-muted me-3">| <i class="bi bi-clock"></i> {{ $event->event_time }}</span> @endif
        </div>
        <div class="text-end">
            <div class="mb-1"><strong style="color: #e91e90; font-size: 24px;">{{ $bookings->count() }}</strong><span class="text-muted"> / {{ config('theatre.vip_seats') }}</span></div>
            <small class="text-muted">مقعد وفود محجوز</small>
        </div>
    </div>
    <div class="progress mt-3" style="height: 8px;">
        <div class="progress-bar" style="width: {{ (config('theatre.vip_seats') > 0) ? ($bookings->count() / config('theatre.vip_seats')) * 100 : 0 }}%; background: linear-gradient(135deg, #e91e90, #9b59b6);"></div>
    </div>
</div>

{{-- دليل الألوان --}}
<div class="card-custom p-3 mb-4">
    <div class="d-flex gap-4 flex-wrap">
        <span><span style="display:inline-block;width:20px;height:20px;border-radius:6px;background:#27ae60;vertical-align:middle;"></span> متاح — اضغط للحجز</span>
        <span><span style="display:inline-block;width:20px;height:20px;border-radius:6px;background:#e91e90;vertical-align:middle;"></span> محجوز لضيف</span>
    </div>
</div>

{{-- مقاعد الوفود حسب القسم --}}
@php
    $sections = $vipSeats->groupBy(fn($s) => $s->section->name);
@endphp

@foreach($sections as $sectionName => $seats)
<div class="card-custom p-4 mb-4">
    <h6 class="mb-3"><i class="bi bi-grid-3x3-gap"></i> القسم {{ $sectionName }} — الصف 10 ({{ $seats->count() }} مقعد)</h6>
    <div class="d-flex flex-wrap gap-2">
        @foreach($seats as $seat)
        @php $booking = $bookings->get($seat->id); @endphp

        @if($booking)
            {{-- مقعد محجوز --}}
            <div class="position-relative" style="width: 120px;">
                <div style="background:#e91e90;color:#fff;border-radius:10px;padding:8px;text-align:center;font-size:12px;">
                    <div style="font-weight:700;">{{ $seat->label }}</div>
                    <div style="font-size:10px;margin-top:2px;">{{ $booking->guest_name }}</div>
                </div>
                <div class="d-flex gap-1 mt-1">
                    {{-- زر واتساب --}}
                    <a href="{{ $this->getWhatsAppLink($booking->id) }}" target="_blank" class="btn btn-sm flex-fill" style="background:#25D366;color:#fff;font-size:10px;padding:3px;">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    {{-- زر إلغاء --}}
                    <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="إلغاء حجز {{ $booking->guest_name }}؟" class="btn btn-sm btn-outline-danger flex-fill" style="font-size:10px;padding:3px;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        @else
            {{-- مقعد متاح --}}
            <div style="width: 120px; cursor: pointer;" wire:click="selectSeat({{ $seat->id }})" data-bs-toggle="modal" data-bs-target="#bookSeatModal">
                <div style="background:#27ae60;color:#fff;border-radius:10px;padding:8px;text-align:center;font-size:12px;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="font-weight:700;">{{ $seat->label }}</div>
                    <div style="font-size:10px;margin-top:2px;">متاح</div>
                </div>
            </div>
        @endif
        @endforeach
    </div>
</div>
@endforeach

{{-- قائمة الوفود المحجوزين --}}
@if($bookings->count() > 0)
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-people"></i> قائمة الوفود ({{ $bookings->count() }})</h6>
        {{-- زر إرسال الكل --}}
        <div class="d-flex gap-2">
            <button class="btn btn-sm" style="background:#25D366;color:#fff;" onclick="document.querySelectorAll('.wa-link').forEach(a => window.open(a.href, '_blank'))">
                <i class="bi bi-whatsapp"></i> إرسال الكل عبر واتساب
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>الضيف</th><th>الجوال</th><th>المقعد</th><th>القسم</th><th>الإجراءات</th></tr></thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $booking->guest_name }}</strong></td>
                    <td dir="ltr">{{ $booking->guest_phone }}</td>
                    <td><span class="badge bg-primary">{{ $booking->seat->label }}</span></td>
                    <td>القسم {{ $booking->seat->section->name }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ $this->getWhatsAppLink($booking->id) }}" target="_blank" class="btn wa-link" style="background:#25D366;color:#fff;" title="إرسال واتساب">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="إلغاء حجز {{ $booking->guest_name }}؟" class="btn btn-outline-danger" title="إلغاء">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- زر الرجوع --}}
<div class="text-center mt-3">
    <a href="{{ route('dashboard.events') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-right"></i> الرجوع للفعاليات</a>
</div>

{{-- نافذة حجز مقعد --}}
<div class="modal fade" id="bookSeatModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-star"></i> حجز مقعد وفود</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @if($errorMessage)
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ $errorMessage }}</div>
                @endif
                @error('guestName')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('guestPhone')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror

                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الضيف <span class="text-danger">*</span></label>
                    <input type="text" wire:model="guestName" class="form-control" placeholder="الاسم الكامل للضيف">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم الجوال <span class="text-danger">*</span></label>
                    <input type="text" wire:model="guestPhone" class="form-control" dir="ltr" placeholder="07701234567">
                    <small class="text-muted">سيتم إرسال دعوة واتساب لهذا الرقم</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button wire:click="bookSeat" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="bookSeat"><i class="bi bi-check-lg"></i> حجز المقعد</span>
                    <span wire:loading wire:target="bookSeat"><span class="wire-loading"></span> جاري الحجز...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('close-modal', () => {
        document.querySelectorAll('.modal').forEach(m => bootstrap.Modal.getInstance(m)?.hide());
    });
</script>
@endscript

</div>
