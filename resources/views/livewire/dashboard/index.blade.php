<div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- 👑 مدير النظام --}}
@if($roleName === 'super_admin')
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card" style="border-bottom: 3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color: #9b59b6;">{{ $totalUsers }}</div><div class="label">إجمالي المستخدمين</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-people-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom: 3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color: #27ae60;">{{ $activeUsers }}</div><div class="label">حسابات فعالة</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-person-check-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom: 3px solid #e91e90;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color: #e91e90;">{{ $inactiveUsers }}</div><div class="label">حسابات معطّلة</div></div><div class="icon" style="background:#fdf2f8;color:#e91e90;"><i class="bi bi-person-x-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom: 3px solid #8e44ad;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color: #8e44ad;">{{ $totalRoles }}</div><div class="label">الأدوار</div></div><div class="icon" style="background:#f4ecf7;color:#8e44ad;"><i class="bi bi-shield-lock-fill"></i></div></div></div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h6 class="mb-3"><i class="bi bi-pie-chart-fill"></i> توزيع الأدوار</h6>
            <table class="table table-hover mb-0"><thead><tr><th>الدور</th><th>العدد</th><th>النسبة</th></tr></thead><tbody>
            @foreach($rolesDistribution as $role)
            <tr><td><span class="badge-role" style="background:{{ $role['color'] }}15;color:{{ $role['color'] }};border:1px solid {{ $role['color'] }}30;">{{ $role['display_name'] }}</span></td><td><strong>{{ $role['count'] }}</strong></td><td><div class="progress" style="height:8px;width:120px;background:#f5eef8;"><div class="progress-bar" style="width:{{ $totalUsers>0?($role['count']/$totalUsers)*100:0 }}%;background:{{ $role['color'] }};"></div></div></td></tr>
            @endforeach
            </tbody></table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4">
            <h6 class="mb-3"><i class="bi bi-info-circle-fill"></i> معلومات المسرح</h6>
            <ul class="list-unstyled">
                <li class="mb-3 d-flex justify-content-between"><span class="text-muted">إجمالي المقاعد</span><strong style="color:#9b59b6;">{{ config('theatre.total_seats') }}</strong></li>
                <li class="mb-3 d-flex justify-content-between"><span class="text-muted">مقاعد الوفود</span><strong style="color:#e91e90;">{{ config('theatre.vip_seats') }}</strong></li>
                <li class="mb-3 d-flex justify-content-between"><span class="text-muted">أقسام المسرح</span><strong>{{ config('theatre.sections') }}</strong></li>
                <li class="d-flex justify-content-between"><span class="text-muted">حالات الفعاليات</span><strong>{{ config('theatre.statuses') }}</strong></li>
            </ul>
        </div>
    </div>
</div>
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="mb-0"><i class="bi bi-clock-history"></i> آخر المستخدمين</h6><a href="{{ route('dashboard.users') }}" class="btn btn-sm btn-primary">عرض الكل</a></div>
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>الحالة</th></tr></thead><tbody>
    @foreach($recentUsers as $user)
    <tr><td>{{ $user->id }}</td><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}</td><td><span class="badge bg-primary">{{ $user->role->display_name }}</span></td><td>@if($user->is_active)<span class="badge bg-success">فعال</span>@else<span class="badge bg-danger">معطّل</span>@endif</td></tr>
    @endforeach
    </tbody></table>
</div>

{{-- 🎭 مدير المسرح — ينشئ الفعاليات --}}
@elseif($roleName === 'theater_manager')
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#9b59b6;">{{ $totalEvents }}</div><div class="label">فعالياتي</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-calendar-event-fill"></i></div></div></div></div>
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#27ae60;">{{ $publishedEvents }}</div><div class="label">منشورة</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-megaphone-fill"></i></div></div></div></div>
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #e91e90;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#e91e90;">{{ $draftEvents }}</div><div class="label">مسودات</div></div><div class="icon" style="background:#fdf2f8;color:#e91e90;"><i class="bi bi-file-earmark-text-fill"></i></div></div></div></div>
</div>
<div class="card-custom p-4">
    <h6 class="mb-3"><i class="bi bi-calendar-event"></i> آخر فعالياتي</h6>
    @if($myEvents->count() > 0)
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>العنوان</th><th>التاريخ</th><th>الحالة</th></tr></thead><tbody>
    @foreach($myEvents as $event)<tr><td>{{ $event->id }}</td><td><strong>{{ $event->title }}</strong></td><td>{{ $event->event_date->format('Y-m-d') }}</td><td><span class="badge bg-primary">{{ $event->status->display_name }}</span></td></tr>@endforeach
    </tbody></table>
    @else<p class="text-muted text-center py-4"><i class="bi bi-inbox" style="font-size:40px;"></i><br>لا توجد فعاليات بعد</p>@endif
</div>
<div class="card-custom p-4 mt-4"><h6><i class="bi bi-info-circle"></i> دورك</h6><p class="text-muted mb-0">أنت مسؤول عن إنشاء الفعاليات. بعد الإنشاء، مدير الإعلام يراجعها ويحجز مقاعد الوفود وينشرها.</p></div>

{{-- 📢 مدير الإعلام — يحجز وفود + ينشر --}}
@elseif($roleName === 'event_manager')
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #f39c12;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#f39c12;">{{ $totalEvents }}</div><div class="label">إجمالي الفعاليات</div></div><div class="icon" style="background:#fef9e7;color:#f39c12;"><i class="bi bi-calendar-event-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#9b59b6;">{{ $pendingReview }}</div><div class="label">بانتظار المراجعة</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-hourglass-split"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#27ae60;">{{ $publishedEvents }}</div><div class="label">منشورة</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-megaphone-fill"></i></div></div></div></div>
    <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid #e91e90;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#e91e90;">{{ config('theatre.vip_seats') }}</div><div class="label">مقاعد الوفود</div></div><div class="icon" style="background:#fdf2f8;color:#e91e90;"><i class="bi bi-star-fill"></i></div></div></div></div>
</div>
<div class="card-custom p-4">
    <h6 class="mb-3"><i class="bi bi-calendar-event"></i> الفعاليات</h6>
    @if($allEvents->count() > 0)
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>العنوان</th><th>التاريخ</th><th>الحالة</th><th>أنشأها</th></tr></thead><tbody>
    @foreach($allEvents as $event)<tr><td>{{ $event->id }}</td><td><strong>{{ $event->title }}</strong></td><td>{{ $event->event_date->format('Y-m-d') }}</td><td><span class="badge bg-primary">{{ $event->status->display_name }}</span></td><td>{{ $event->creator->name }}</td></tr>@endforeach
    </tbody></table>
    @else<p class="text-muted text-center py-4"><i class="bi bi-inbox" style="font-size:40px;"></i><br>لا توجد فعاليات</p>@endif
</div>
<div class="card-custom p-4 mt-4"><h6><i class="bi bi-info-circle"></i> دورك</h6><p class="text-muted mb-0">أنت مسؤول عن: مراجعة الفعاليات، حجز مقاعد الوفود ({{ config('theatre.vip_seats') }} مقعد في الصف 10)، ونشر الفعاليات للجمهور.</p></div>

{{-- 📋 موظف الاستقبال --}}
@elseif($roleName === 'receptionist')
<div class="row g-4 mb-4">
    <div class="col-md-6"><div class="stat-card" style="border-bottom:3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#27ae60;">{{ $checkedInToday }}</div><div class="label">تم تسجيل حضورهم اليوم</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-qr-code-scan"></i></div></div></div></div>
    <div class="col-md-6"><div class="stat-card" style="border-bottom:3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#9b59b6;">{{ $totalReservations }}</div><div class="label">إجمالي الحجوزات</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-ticket-perforated-fill"></i></div></div></div></div>
</div>
<div class="card-custom p-5 text-center">
    <i class="bi bi-qr-code-scan" style="font-size:70px;color:#9b59b6;"></i>
    <h4 class="mt-3" style="color:#7b2d8e;">جاهز لتسجيل الحضور</h4>
    <p class="text-muted">امسح رمز QR من التذكرة</p>
    <a href="{{ route('dashboard.checkin') }}" class="btn btn-primary btn-lg mt-2"><i class="bi bi-qr-code-scan"></i> بدء تسجيل الحضور</a>
</div>

{{-- 📊 مكتب الرئيس --}}
@elseif($roleName === 'university_office')
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #9b59b6;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#9b59b6;">{{ $totalEvents }}</div><div class="label">إجمالي الفعاليات</div></div><div class="icon" style="background:#f5eef8;color:#9b59b6;"><i class="bi bi-calendar-event-fill"></i></div></div></div></div>
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #f39c12;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#f39c12;">{{ $totalReservations }}</div><div class="label">إجمالي الحجوزات</div></div><div class="icon" style="background:#fef9e7;color:#f39c12;"><i class="bi bi-ticket-perforated-fill"></i></div></div></div></div>
    <div class="col-md-4"><div class="stat-card" style="border-bottom:3px solid #27ae60;"><div class="d-flex justify-content-between align-items-center"><div><div class="number" style="color:#27ae60;">{{ $totalCheckedIn }}</div><div class="label">إجمالي الحضور</div></div><div class="icon" style="background:#e8f8f5;color:#27ae60;"><i class="bi bi-person-check-fill"></i></div></div></div></div>
</div>
<div class="card-custom p-4">
    <h6><i class="bi bi-bar-chart-line"></i> ملخص النظام</h6>
    <div class="row g-3 mt-2">
        <div class="col-md-6"><div class="p-3 rounded" style="background:#f5f0ff;"><span class="text-muted">إجمالي المقاعد</span><h3 style="color:#9b59b6;">{{ config('theatre.total_seats') }}</h3><small class="text-muted">منها {{ config('theatre.vip_seats') }} مقعد وفود</small></div></div>
        <div class="col-md-6"><div class="p-3 rounded" style="background:#fdf2f8;"><span class="text-muted">نسبة الحضور</span><h3 style="color:#e91e90;">{{ $totalReservations > 0 ? round(($totalCheckedIn/$totalReservations)*100,1) : 0 }}%</h3><small class="text-muted">{{ $totalCheckedIn }} من {{ $totalReservations }}</small></div></div>
    </div>
</div>
@endif

</div>
