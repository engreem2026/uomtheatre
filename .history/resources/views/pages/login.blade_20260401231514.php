<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — {{ config('theatre.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        body { background: linear-gradient(135deg, #4a1068 0%, #2d0845 30%, #1a0533 60%, #e91e90 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 20px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 25px 60px rgba(74, 16, 104, 0.4); border-top: 4px solid #e91e90; }
        .login-card .logo { text-align: center; margin-bottom: 25px; }
        .login-card .logo img { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #f8c8dc; object-fit: cover; background: #fff; }
        .login-card .logo h3 { color: #4a1068; margin-top: 12px; font-weight: 700; }
        .login-card .logo p { color: #888; font-size: 14px; }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 2px solid #f0e0f0; }
        .form-control:focus { border-color: #9b59b6; box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.15); }
        .form-label { color: #4a1068; font-weight: 600; }
        .btn-login { background: linear-gradient(135deg, #e91e90, #9b59b6); border: none; border-radius: 10px; padding: 12px; font-size: 16px; font-weight: 600; width: 100%; color: #fff; }
        .btn-login:hover { background: linear-gradient(135deg, #9b59b6, #e91e90); color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(233, 30, 144, 0.3); }
        .btn-login:disabled { opacity: 0.7; }
        .wire-loading { display: inline-block; width: 16px; height: 16px; border: 2px solid #fff; border-radius: 50%; border-top-color: transparent; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <livewire:auth.login />
    @livewireScripts
</body>
</html>
