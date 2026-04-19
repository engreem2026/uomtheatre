@extends('layouts.app')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'إدارة المستخدمين والأدوار')

@section('content')
<!-- Action Bar -->
<div class="card-custom p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted">إجمالي: <strong>{{ $users->count() }}</strong> مستخدم</span>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus"></i> إنشاء مستخدم جديد
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الجوال</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        @php
                            $roleColors = [
                                'super_admin' => '#e74c3c',
                                'event_manager' => '#f39c12',
                                'theater_manager' => '#2e75b6',
                                'receptionist' => '#27ae60',
                                'university_office' => '#8e44ad',
                                'user' => '#95a5a6',
                            ];
                            $color = $roleColors[$user->role->name] ?? '#95a5a6';
                        @endphp
                        <span class="badge-role" style="background: {{ $color }}20; color: {{ $color }}; border: 1px solid {{ $color }}40;">
                            {{ $user->role->display_name }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> فعال</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> معطّل</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <!-- Edit Button -->
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <!-- Toggle Status -->
                            <form action="{{ route('dashboard.users.toggle', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                @if($user->is_active)
                                    <button type="submit" class="btn btn-outline-danger" title="تعطيل" onclick="return confirm('هل تريد تعطيل حساب {{ $user->name }}؟')">
                                        <i class="bi bi-person-x"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-outline-success" title="تفعيل" onclick="return confirm('هل تريد تفعيل حساب {{ $user->name }}؟')">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                @endif
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Modal for each user -->
                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('dashboard.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">تعديل: {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">الاسم</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">رقم الموبايل </label>
                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">الدور</label>
                                        <select name="role_id" class="form-select">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">كلمة مرور جديدة (اختياري)</label>
                                        <input type="password" name="password" class="form-control" placeholder="اتركيه فارغ إذا ما تبين تغيّرينه">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mt-2">لا يوجد مستخدمين</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Roles Legend -->
<div class="card-custom p-4 mt-4">
    <h6 class="mb-3"><i class="bi bi-shield-lock"></i> الأدوار والصلاحيات</h6>
    <div class="row g-3">
        @foreach($roles as $role)
        @php
            $colors = [
                'super_admin' => '#e74c3c',
                'event_manager' => '#f39c12',
                'theater_manager' => '#2e75b6',
                'receptionist' => '#27ae60',
                'university_office' => '#8e44ad',
                'user' => '#95a5a6',
            ];
            $c = $colors[$role->name] ?? '#95a5a6';
        @endphp
        <div class="col-md-4">
            <div class="p-3 rounded" style="background: {{ $c }}10; border-right: 4px solid {{ $c }};">
                <strong style="color: {{ $c }};">{{ $role->display_name }}</strong>
                <br><small class="text-muted">{{ $role->description }}</small>
                <br><code class="small">{{ $role->name }}</code>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> إنشاء مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="الاسم الكامل" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="example@uom.edu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="6 أحرف على الأقل" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الجوال</label>
                        <input type="text" name="phone" class="form-control" placeholder="07xxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الدور <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">اختر الدور...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }} — {{ $role->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> إنشاء المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
