<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'لوحة التحكم' }} — {{ config('theatre.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --primary: #9b59b6; --primary-dark: #7b2d8e; --primary-light: #c39bd3;
            --pink: #e91e90; --pink-light: #f8c8dc; --pink-bg: #fdf2f8;
            --purple-bg: #f5f0ff; --sidebar-top: #4a1068; --sidebar-bottom: #2d0845;
            --accent: #d63384; --accent-light: #f0d0e0;
        }
        body { background: linear-gradient(135deg, var(--pink-bg) 0%, var(--purple-bg) 100%); font-family: 'Segoe UI', Tahoma, sans-serif; min-height: 100vh; }
        .sidebar { background: linear-gradient(180deg, var(--sidebar-top) 0%, var(--sidebar-bottom) 100%); min-height: 100vh; width: 260px; position: fixed; right: 0; top: 0; z-index: 100; box-shadow: -4px 0 20px rgba(75, 0, 130, 0.2); }
        .sidebar .logo { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo img { width: 70px; height: 70px; border-radius: 50%; border: 3px solid var(--pink-light); object-fit: cover; margin-bottom: 10px; background: #fff; }
        .sidebar .logo h5 { color: #fff; margin: 5px 0 2px; font-weight: 700; }
        .sidebar .logo small { color: var(--pink-light); font-size: 11px; }
        .sidebar .role-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; background: rgba(233, 30, 144, 0.3); color: var(--pink-light); margin-top: 5px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 12px 20px; border-radius: 10px; margin: 4px 12px; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover { background: rgba(233, 30, 144, 0.2); color: #fff; transform: translateX(-3px); }
        .sidebar .nav-link.active { background: linear-gradient(135deg, var(--pink), var(--primary)); color: #fff; box-shadow: 0 4px 15px rgba(233, 30, 144, 0.3); }
        .sidebar .nav-link i { margin-left: 10px; font-size: 18px; }
        .main-content { margin-right: 260px; padding: 20px; }
        .top-bar { background: #fff; padding: 15px 25px; border-radius: 14px; margin-bottom: 20px; box-shadow: 0 2px 15px rgba(155, 89, 182, 0.08); display: flex; justify-content: space-between; align-items: center; }
        .top-bar h5 { color: var(--primary-dark); margin: 0; }
        .top-bar .badge { background: linear-gradient(135deg, var(--pink), var(--primary)) !important; }
        .stat-card { background: #fff; border-radius: 14px; padding: 22px; box-shadow: 0 3px 15px rgba(155, 89, 182, 0.08); transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(155, 89, 182, 0.15); }
        .stat-card .number { font-size: 30px; font-weight: 800; }
        .stat-card .label { color: #888; font-size: 13px; margin-top: 4px; }
        .stat-card .icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .card-custom { background: #fff; border-radius: 14px; border: none; box-shadow: 0 3px 15px rgba(155, 89, 182, 0.08); }
        .card-custom h6 { color: var(--primary-dark); font-weight: 700; }
        .table th { background: linear-gradient(135deg, #fdf2f8, #f5f0ff); font-weight: 600; font-size: 13px; color: var(--primary-dark); border-bottom: 2px solid var(--accent-light); }
        .table td { font-size: 14px; vertical-align: middle; }
        .table tbody tr:hover { background: var(--pink-bg); }
        .badge-role { padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .btn-primary { background: linear-gradient(135deg, var(--pink), var(--primary)); border: none; border-radius: 10px; font-weight: 600; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--primary-dark), var(--pink)); transform: translateY(-1px); box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; }
        .modal-header { background: linear-gradient(135deg, var(--pink-bg), var(--purple-bg)); border-bottom: 2px solid var(--accent-light); }
        .modal-header .modal-title { color: var(--primary-dark); font-weight: 700; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.15); }
        .form-label { color: var(--primary-dark); }
        .alert-success { background: #e8f8f5; border-color: #27ae60; color: #1e8449; border-radius: 10px; }
        .alert-danger { background: #fdedec; border-color: #e74c3c; color: #c0392b; border-radius: 10px; }
        /* Livewire loading indicator */
        [wire\:loading] { opacity: 0.6; }
        .wire-loading { display: inline-block; width: 16px; height: 16px; border: 2px solid #fff; border-radius: 50%; border-top-color: transparent; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="شعار {{ config('theatre.university') }}"
                 onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';">
            <div id="logo-fallback" style="display:none; font-size: 40px;">🎭</div>
            <h5>{{ config('theatre.name') }}</h5>
            <small>{{ config('theatre.hall_name') }}</small>
            <br><span class="role-badge">{{ session('user_role') }}</span>
        </div>
        <nav class="mt-3">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> الرئيسية
            </a>
            @if(session('role_name') === 'super_admin')
            <a href="{{ route('dashboard.users') }}" class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <i class="bi bi-people"></i> إدارة المستخدمين
            </a>
            @endif
            @if(in_array(session('role_name'), ['super_admin', 'theater_manager', 'event_manager']))
            <a href="{{ route('dashboard.events') }}" class="nav-link {{ request()->routeIs('dashboard.events') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> الفعاليات
            </a>
            @endif
            @if(in_array(session('role_name'), ['super_admin', 'event_manager']))
            <a href="{{ route('dashboard.events') }}" class="nav-link">
                <i class="bi bi-star"></i> مقاعد الوفود ({{ config('theatre.vip_seats') }})
            </a>
            @endif
            @if(in_array(session('role_name'), ['super_admin', 'receptionist']))
            <a href="{{ route('dashboard.checkin') }}" class="nav-link {{ request()->routeIs('dashboard.checkin') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> تسجيل الحضور
            </a>
            @endif






            @if(session('role_name') === 'super_admin')
            <a href="{{ route('dashboard.users') }}" class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <i class="bi bi-people"></i> إدارة المستخدمين
            </a>
            <a href="{{ route('dashboard.staff') }}" class="nav-link {{ request()->routeIs('dashboard.staff') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> إدارة الموظفين
            </a>
            @endif
            <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 20px;">
            <a href="{{ route('dashboard.logout') }}" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> تسجيل خروج
            </a>
        </nav>
    </div>
    <div class="main-content">
        <div class="top-bar">
            <h5>{{ $title ?? 'لوحة التحكم' }}</h5>
            <span class="text-muted">
                <i class="bi bi-person-circle" style="color: var(--primary);"></i>
                {{ session('user_name') }}
                <span class="badge ms-2">{{ session('user_role') }}</span>
            </span>
        </div>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   @livewireScripts
</body>
</html>
