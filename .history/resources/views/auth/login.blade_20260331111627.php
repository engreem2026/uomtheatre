<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — rقاعة محمود الجليلي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4a1068 0%, #2d0845 30%, #1a0533 60%, #e91e90 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(74, 16, 104, 0.4);
            border-top: 4px solid #e91e90;
        }
        .login-card .logo { text-align: center; margin-bottom: 25px; }
        .login-card .logo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #f8c8dc;
            object-fit: cover;
            background: #fff;
        }
        .login-card .logo h3 { color: #4a1068; margin-top: 12px; font-weight: 700; }
        .login-card .logo p { color: #888; font-size: 14px; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #f0e0f0;
        }
        .form-control:focus {
            border-color: #9b59b6;
            box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.15);
        }
        .form-label { color: #4a1068; font-weight: 600; }
        .btn-login {
            background: linear-gradient(135deg, #e91e90, #9b59b6);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            color: #fff;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #9b59b6, #e91e90);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 30, 144, 0.3);
        }
        .alert-danger {
            border-radius: 10px;
            border-right: 4px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <!-- ===== مكان الشعار ===== -->
            <img src="{{ asset('images/logo.png') }}" alt="شعار جامعة الموصل"
                 onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';">
            <div id="logo-fallback" style="display:none; font-size: 50px;">🎭</div>
            <h3>قاعة محمود الجليلي</h3>
            <p>لوحة تحكم مدير النظام</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="@uomtheatre.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">الحساب الافتراضي: Reem@uomtheatre.com / password123</small>
        </div>
    </div>
</body>
</html>
