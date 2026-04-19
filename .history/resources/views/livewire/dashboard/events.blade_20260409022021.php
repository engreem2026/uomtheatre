<div>

@if($successMessage)
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> {{ $successMessage }}<button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="$set('successMessage', '')"></button></div>
@endif

{{-- شريط الإجراءات --}}
<div class="card-custom p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">إجمالي الفعاليات: <strong>{{ $events->count() }}</strong></span>
        @if(in_array($roleName, ['super_admin', 'theater_manager']))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
            <i class="bi bi-plus-circle"></i> إنشاء فعالية جديدة
        </button>
        @endif
    </div>
</div>

{{-- جدول الفعاليات --}}
<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان</th>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>الحالة</th>
                    <th>أنشأها</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                @php
                    $statusColors = [
                        'draft' => '#6B7280', 'added' => '#3B82F6', 'under_review' => '#F59E0B',
                        'active' => '#8B5CF6', 'published' => '#10B981', 'closed' => '#EF4444',
                        'cancelled' => '#DC2626', 'end' => '#9CA3AF',
                    ];
                    $statusNames = [
                        'draft' => 'مسودة', 'added' => 'مضافة', 'under_review' => 'قيد المراجعة',
                        'active' => 'نشطة', 'published' => 'منشورة', 'closed' => 'مغلقة',
                        'cancelled' => 'ملغاة', 'end' => 'منتهية',
                    ];
                    $sName = $event->status->name;
                    $sColor = $statusColors[$sName] ?? '#6B7280';
                    $sLabel = $statusNames[$sName] ?? $sName;
                @endphp
                <tr>
                    <td>{{ $event->id }}</td>
                    <td>
                        <strong>{{ $event->title }}</strong>
                        @if($event->description)
                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($event->description, 50) }}</small>
                        @endif
                    </td>
                    <td>{{ $event->event_date->format('Y-m-d') }}</td>
                    <td>{{ $event->event_time ?? '—' }}</td>
                    <td>
                        <span class="badge-role" style="background:{{ $sColor }}20;color:{{ $sColor }};border:1px solid {{ $sColor }}40;">
                            {{ $sLabel }}
                        </span>
                    </td>
                    <td>{{ $event->creator->name }}</td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            {{-- ═══ أزرار مدير المسرح ═══ --}}
                            @if(in_array($roleName, ['super_admin', 'theater_manager']))
                                {{-- تعديل (فقط إذا مسودة) --}}
                                @if($sName === 'draft')
                                <button class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $event->id }})" data-bs-toggle="modal" data-bs-target="#editEventModal" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- إرسال للمراجعة --}}
                                <button class="btn btn-sm btn-outline-info" wire:click="changeStatus({{ $event->id }}, 'added')" wire:confirm="إرسال الفعالية للمراجعة؟" title="إرسال للمراجعة">
                                    <i class="bi bi-send"></i>
                                </button>
                                @endif
                            @endif


                            

                            {{-- ═══ أزرار مدير الإعلام ═══ --}}
                            @if(in_array($roleName, ['super_admin', 'event_manager']))
                                {{-- بدء المراجعة --}}
                                @if($sName === 'added')
                                <button class="btn btn-sm btn-outline-warning" wire:click="changeStatus({{ $event->id }}, 'under_review')" wire:confirm="بدء مراجعة هذه الفعالية؟" title="بدء المراجعة">
                                    <i class="bi bi-eye"></i> مراجعة
                                </button>
                                @endif

                                {{-- قبول → نشطة --}}
                                @if($sName === 'under_review')
                                <button class="btn btn-sm btn-outline-success" wire:click="changeStatus({{ $event->id }}, 'active')" wire:confirm="قبول الفعالية؟" title="قبول">
                                    <i class="bi bi-check-lg"></i> قبول
                                </button>
                                @endif

                                {{-- حجز مقاعد الوفود (نشطة أو قيد المراجعة) --}}
                                @if(in_array($sName, ['active', 'under_review']))
                                <a href="{{ route('dashboard.vip-booking', $event->id) }}" class="btn btn-sm btn-outline-danger" title="حجز مقاعد الوفود">
                                    <i class="bi bi-star"></i> وفود
                                </a>
                                @endif

                                {{-- نشر --}}
                                @if($sName === 'active')
                                <button class="btn btn-sm btn-success" wire:click="changeStatus({{ $event->id }}, 'published')" wire:confirm="نشر الفعالية للجمهور؟" title="نشر">
                                    <i class="bi bi-megaphone"></i> نشر
                                </button>
                                @endif

                                {{-- إغلاق --}}
                                @if($sName === 'published')
                                <button class="btn btn-sm btn-outline-secondary" wire:click="changeStatus({{ $event->id }}, 'closed')" wire:confirm="إغلاق الفعالية؟" title="إغلاق">
                                    <i class="bi bi-lock"></i>
                                </button>
                                {{-- حجز الوفود (حتى بعد النشر يقدر يشوف القائمة) --}}
                                <a href="{{ route('dashboard.vip-booking', $event->id) }}" class="btn btn-sm btn-outline-danger" title="قائمة الوفود">
                                    <i class="bi bi-star"></i>
                                </a>
                                @endif
                            @endif

                            {{-- ═══ إلغاء (الكل) ═══ --}}
                            @if(!in_array($sName, ['cancelled', 'end', 'closed']))
                            <button class="btn btn-sm btn-outline-danger" wire:click="changeStatus({{ $event->id }}, 'cancelled')" wire:confirm="هل تريد إلغاء الفعالية؟ سيتم إلغاء كل الحجوزات!" title="إلغاء">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-calendar-x" style="font-size:40px;color:#c39bd3;"></i><p class="mt-2">لا توجد فعاليات</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- دليل الحالات --}}
<div class="card-custom p-3 mt-4">
    <h6 class="mb-2"><i class="bi bi-info-circle"></i> تدفق حالات الفعالية</h6>
    <div class="d-flex flex-wrap gap-2 align-items-center small">
        <span class="badge" style="background:#6B7280;">مسودة</span>
        <i class="bi bi-arrow-left"></i>
        <span class="badge" style="background:#3B82F6;">مضافة</span>
        <i class="bi bi-arrow-left"></i>
        <span class="badge" style="background:#F59E0B;">قيد المراجعة</span>
        <i class="bi bi-arrow-left"></i>
        <span class="badge" style="background:#8B5CF6;">نشطة</span>
        <i class="bi bi-arrow-left"></i>
        <span class="badge" style="background:#10B981;">منشورة</span>
        <i class="bi bi-arrow-left"></i>
        <span class="badge" style="background:#EF4444;">مغلقة</span>
    </div>
</div>

{{-- نافذة إنشاء فعالية --}}
<div class="modal fade" id="createEventModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle"></i> إنشاء فعالية جديدة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @error('title')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('event_date')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror

                <div class="mb-3"><label class="form-label fw-bold">عنوان الفعالية <span class="text-danger">*</span></label><input type="text" wire:model="title" class="form-control" placeholder="مثال: حفل تخرج كلية الهندسة"></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea wire:model="description" class="form-control" rows="3" placeholder="وصف مختصر للفعالية..."></textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">التاريخ <span class="text-danger">*</span></label><input type="date" wire:model="event_date" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">الوقت</label><input type="text" wire:model="event_time" class="form-control" placeholder="مثال: 6:00 مساءً"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button wire:click="createEvent" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="createEvent"><i class="bi bi-plus-circle"></i> إنشاء</span>
                    <span wire:loading wire:target="createEvent"><span class="wire-loading"></span> جاري الإنشاء...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- نافذة تعديل فعالية --}}
<div class="modal fade" id="editEventModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil"></i> تعديل الفعالية</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-bold">العنوان</label><input type="text" wire:model="editTitle" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea wire:model="editDescription" class="form-control" rows="3"></textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">التاريخ</label><input type="date" wire:model="editDate" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">الوقت</label><input type="text" wire:model="editTime" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button wire:click="updateEvent" class="btn btn-primary" wire:loading.attr="disabled">حفظ التعديلات</button>
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
