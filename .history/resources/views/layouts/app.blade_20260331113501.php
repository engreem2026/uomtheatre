<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') — قاعة محمود الجليلي </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #9b59b6;
            --primary-dark: #7b2d8e;
            --primary-light: #c39bd3;
            --pink: #e91e90;
            --pink-light: #f8c8dc;
            --pink-bg: #fdf2f8;
            --purple-bg: #f5f0ff;
            --sidebar-top: #4a1068;
            --sidebar-bottom: #2d0845;
            --accent: #d63384;
            --accent-light: #f0d0e0;
        }
        body { background: linear-gradient(135deg, var(--pink-bg) 0%, var(--purple-bg) 100%); font-family: 'Segoe UI', Tahoma, sans-serif; min-height: 100vh; }

        /* ======= SIDEBAR ======= */
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-top) 0%, var(--sidebar-bottom) 100%);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 100;
            box-shadow: -4px 0 20px rgba(75, 0, 130, 0.2);
        }
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        /* ===== مكان الشعار ===== */
        .sidebar .logo img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid var(--pink-light);
            object-fit: cover;
            margin-bottom: 10px;
            background: #fff;
        }
        .sidebar .logo h5 { color: #fff; margin: 5px 0 2px; font-weight: 700; }
        .sidebar .logo small { color: var(--pink-light); font-size: 11px; }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 12px;
            transition: all 0.3s;
            font-size: 14px;
        }
        .sidebar .nav-link:hover {
            background: rgba(233, 30, 144, 0.2);
            color: #fff;
            transform: translateX(-3px);
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--pink), var(--primary));
            color: #fff;
            box-shadow: 0 4px 15px rgba(233, 30, 144, 0.3);
        }
        .sidebar .nav-link i { margin-left: 10px; font-size: 18px; }
        .sidebar .nav-link.text-danger:hover { background: rgba(220, 53, 69, 0.2); }

        /* ======= MAIN CONTENT ======= */
        .main-content { margin-right: 260px; padding: 20px; }

        .top-bar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 2px 15px rgba(155, 89, 182, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 3px solid linear-gradient(90deg, var(--pink), var(--primary));
        }
        .top-bar h5 { color: var(--primary-dark); }
        .top-bar .badge { background: linear-gradient(135deg, var(--pink), var(--primary)) !important; }

        /* ======= STAT CARDS ======= */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 3px 15px rgba(155, 89, 182, 0.08);
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(155, 89, 182, 0.15);
        }
        .stat-card:nth-child(1) .stat-card { border-bottom-color: var(--primary); }
        .stat-card .number { font-size: 30px; font-weight: 800; }
        .stat-card .label { color: #888; font-size: 13px; margin-top: 4px; }
        .stat-card .icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        /* ======= CARDS ======= */
        .card-custom {
            background: #fff;
            border-radius: 14px;
            border: none;
            box-shadow: 0 3px 15px rgba(155, 89, 182, 0.08);
        }
        .card-custom h6 { color: var(--primary-dark); font-weight: 700; }

        /* ======= TABLE ======= */
        .table th {
            background: linear-gradient(135deg, #fdf2f8, #f5f0ff);
            font-weight: 600;
            font-size: 13px;
            color: var(--primary-dark);
            border-bottom: 2px solid var(--accent-light);
        }
        .table td { font-size: 14px; vertical-align: middle; }
        .table tbody tr:hover { background: var(--pink-bg); }

        /* ======= BADGES ======= */
        .badge-role {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.bg-success { background: #27ae60 !important; }
        .badge.bg-danger { background: #e74c3c !important; }
        .badge.bg-primary { background: var(--primary) !important; }

        /* ======= BUTTONS ======= */
        .btn-primary {
            background: linear-gradient(135deg, var(--pink), var(--primary));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--pink));
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3);
        }
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            color: #fff;
        }

        /* ======= MODAL ======= */
        .modal-header {
            background: linear-gradient(135deg, var(--pink-bg), var(--purple-bg));
            border-bottom: 2px solid var(--accent-light);
        }
        .modal-header .modal-title { color: var(--primary-dark); font-weight: 700; }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.15);
        }
        .form-label { color: var(--primary-dark); }

        /* ======= ALERTS ======= */
        .alert-success {
            background: #e8f8f5;
            border-color: #27ae60;
            color: #1e8449;
            border-radius: 10px;
        }
        .alert-danger {
            background: #fdedec;
            border-color: #e74c3c;
            color: #c0392b;
            border-radius: 10px;
        }

        /* ======= PROGRESS BAR ======= */
        .progress { border-radius: 10px; }
        .progress-bar { border-radius: 10px; }

        /* ======= SCROLLBAR ======= */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--pink-bg); }
        ::-webkit-scrollbar-thumb { background: var(--primary-light); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <!--
                ===== مكان الشعار =====
                غيّري مسار الصورة لشعار جامعة الموصل
                ضعي الشعار في: public/images/logo.png
                ثم الرابط يصير: /images/logo.png
            -->
            <img src="{{ asset('images/logo.png') }}" alt="شعار جامعة الموصل"
                 onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';">
            <div id="logo-fallback" style="display:none; font-size: 40px;">🎭</div>
            <!--
                onerror = "إذا الصورة ما تحمّلت، اخفيها واعرض الأيقونة بدالها"
                يعني: لو ما حطيتي الشعار بعد، يظهر 🎭 بدال
            -->
            <h5>UOMTheatre</h5>
            <small>نظام حجز مقاعد المسرح</small>
        </div>
        <nav class="mt-3">
            <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> الرئيسية
            </a>
            <a href="{{ route('dashboard.users') }}" class="nav-link {{ request()->routeIs('dashboard.users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> إدارة المستخدمين
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 20px;">
            <a href="{{ route('dashboard.logout') }}" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> تسجيل خروج
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h5 class="mb-0">@yield('page-title', 'لوحة التحكم')</h5>
            <span class="text-muted">
                <i class="bi bi-person-circle" style="color: var(--primary);"></i>
                {{ session('user_name', 'مدير النظام') }}
                <span class="badge ms-2">{{ session('user_role', 'مدير') }}</span>
            </span>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
