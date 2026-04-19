<div>
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#9b59b6;">{{ $totalEvents }}</div><div class="label">إجمالي الفعاليات</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-calendar-event-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#27ae60;">{{ $publishedEvents }}</div><div class="label">منشورة</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-megaphone-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #f39c12;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#f39c12;">{{ $totalReservations }}</div><div class="label">إجمالي الحجوزات</div></div><div class="icon" style="background:#fef9e7;color:#f39c12;"><i class="bi bi-ticket-perforated-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #e91e90;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#e91e90;">{{ $totalCheckedIn }}</div><div class="label">إجمالي الحضور</div></div><div class="icon" style="background:#fdf2f8;color:#e91e90;"><i class="bi bi-person-check-fill"></i></div></div></div></div>
</div>

<div class="card-custom p-4">
    <h6 class="mb-3"><i class="bi bi-bar-chart-line"></i> ملخص النظام</h6>
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="p-3 rounded" style="background:#f5f0ff;">
                <span class="text-muted">إجمالي المقاعد</span>
                <h3 style="color:#9b59b6;">{{ config('theatre.total_seats') }}</h3>
                <small class="text-muted">منها {{ config('theatre.vip_seats') }} مقعد وفود</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded" style="background:#fdf2f8;">
                <span class="text-muted">نسبة الحضور</span>
                <h3 style="color:#e91e90;">{{ $totalReservations > 0 ? round(($totalCheckedIn/$totalReservations)*100,1) : 0 }}%</h3>
                <small class="text-muted">{{ $totalCheckedIn }} حضروا من {{ $totalReservations }} حجز</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded" style="background:#e8f8f5;">
                <span class="text-muted">أقسام المسرح</span>
                <h3 style="color:#27ae60;">{{ config('theatre.sections') }}</h3>
                <small class="text-muted">A, B, C (عادي) + D, E, F (VIP)</small>
            </div>
        </div>
    </div>
</div>
</div>
