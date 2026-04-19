<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — UOMTheatre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f4e 50%, #0d1235 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-card .logo { text-align: center; margin-bottom: 25px; }
        .login-card .logo h3 { color: #1a1f4e; margin-top: 10px; }
        .login-card .logo p { color: #888; font-size: 14px; }
        .form-control { border-radius: 10px; padding: 12px 15px; }
        .form-control:focus { border-color: #2e75b6; box-shadow: 0 0 0 3px rgba(46,117,182,0.15); }
        .btn-login {
            background: linear-gradient(135deg, #1a1f4e, #2e75b6);
            border: none; border-radius: 10px; padding: 12px;
            font-size: 16px; font-weight: 600; width: 100%; color: #fff;
        }
        .btn-login:hover { background: linear-gradient(135deg, #0d1235, #1a5276); color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <div style="font-size: 50px;">🎭</div>
            <h3>UOMTheatre</h3>
            <p>لوحة تحكم مدير النظام</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="admin@uomtheatre.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">الحساب الافتراضي: @uomtheatre.com / password123</small>
        </div>
    </div>
</body>
</html>
