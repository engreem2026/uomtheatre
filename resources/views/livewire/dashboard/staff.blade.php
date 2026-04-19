<div>

@if($successMessage)
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> {{ $successMessage }}<button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="$set('successMessage', '')"></button></div>
@endif
@error('toggle')<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>@enderror

<div class="card-custom p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted">إجمالي الموظفين: <strong>{{ $staff->count() }}</strong></span>
            <span class="text-muted me-3">| موظفي النظام الذين يدخلون لوحة التحكم</span>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-person-plus"></i> إضافة موظف جديد
        </button>
    </div>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>#</th><th>الاسم</th><th>البريد</th><th>الجوال</th><th>الدور</th><th>الحالة</th><th>التاريخ</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($staff as $member)
                @php
                    $roleColors = ['super_admin'=>'#e74c3c','event_manager'=>'#f39c12','theater_manager'=>'#2e75b6','receptionist'=>'#27ae60','university_office'=>'#8e44ad'];
                    $color = $roleColors[$member->role->name] ?? '#95a5a6';
                @endphp
                <tr>
                    <td>{{ $member->id }}</td>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td><span class="badge-role" style="background:{{ $color }}20;color:{{ $color }};border:1px solid {{ $color }}40;">{{ $member->role->display_name }}</span></td>
                    <td>@if($member->is_active)<span class="badge bg-success"><i class="bi bi-check-circle"></i> فعال</span>@else<span class="badge bg-danger"><i class="bi bi-x-circle"></i> معطّل</span>@endif</td>
                    <td>{{ $member->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" wire:click="openEdit({{ $member->id }})" data-bs-toggle="modal" data-bs-target="#editModal" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($member->is_active)
                            <button class="btn btn-outline-danger" wire:click="toggleStatus({{ $member->id }})" wire:confirm="هل تريد تعطيل حساب {{ $member->name }}؟" title="تعطيل">
                                <i class="bi bi-person-x"></i>
                            </button>
                            @else
                            <button class="btn btn-outline-success" wire:click="toggleStatus({{ $member->id }})" wire:confirm="هل تريد تفعيل حساب {{ $member->name }}؟" title="تفعيل">
                                <i class="bi bi-person-check"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:40px;"></i><p class="mt-2">لا يوجد موظفين</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- نافذة إضافة موظف --}}
<div class="modal fade" id="createModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-person-plus"></i> إضافة موظف جديد</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @error('name')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('email')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('password')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('role_id')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror

                <div class="mb-3"><label class="form-label fw-bold">الاسم <span class="text-danger">*</span></label><input type="text" wire:model="name" class="form-control" placeholder="الاسم الكامل"></div>
                <div class="mb-3"><label class="form-label fw-bold">البريد <span class="text-danger">*</span></label><input type="email" wire:model="email" class="form-control" placeholder="example@uom.edu"></div>
                <div class="mb-3"><label class="form-label fw-bold">كلمة المرور <span class="text-danger">*</span></label><input type="password" wire:model="password" class="form-control" placeholder="6 أحرف على الأقل"></div>
                <div class="mb-3"><label class="form-label fw-bold">الجوال</label><input type="text" wire:model="phone" class="form-control" placeholder="07xxxxxxxxx"></div>
                <div class="mb-3"><label class="form-label fw-bold">الدور <span class="text-danger">*</span></label>
                    <select wire:model="role_id" class="form-select">
                        <option value="0">اختر الدور...</option>
                        @foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->display_name }} — {{ $role->description }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button wire:click="createStaff" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="createStaff"><i class="bi bi-person-plus"></i> إضافة</span>
                    <span wire:loading wire:target="createStaff"><span class="wire-loading"></span> جاري الإضافة...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- نافذة تعديل موظف --}}
<div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil"></i> تعديل الموظف</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @error('editName')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                @error('editEmail')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror

                <div class="mb-3"><label class="form-label fw-bold">الاسم</label><input type="text" wire:model="editName" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">البريد</label><input type="email" wire:model="editEmail" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الجوال</label><input type="text" wire:model="editPhone" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الدور</label>
                    <select wire:model="editRoleId" class="form-select">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->display_name }}</option>@endforeach</select>
                </div>
                <div class="mb-3"><label class="form-label fw-bold">كلمة مرور جديدة (اختياري)</label><input type="password" wire:model="editPassword" class="form-control" placeholder="اتركيه فارغ إذا ما تبين تغيير"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button wire:click="updateStaff" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updateStaff">حفظ التعديلات</span>
                    <span wire:loading wire:target="updateStaff"><span class="wire-loading"></span> جاري الحفظ...</span>
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
