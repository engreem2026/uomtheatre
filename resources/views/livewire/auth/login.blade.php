<div class="login-card">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="شعار {{ config('theatre.university') }}"
             onerror="this.style.display='none'; document.getElementById('logo-fb').style.display='block';">
        <div id="logo-fb" style="display:none; font-size: 50px;">🎭</div>
        <h3>{{ config('theatre.name') }}</h3>
        <p>لوحة تحكم {{ config('theatre.university') }}</p>
    </div>

    @if($errorMessage)
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ $errorMessage }}</div>
    @endif
    @error('email')<div class="alert alert-danger">{{ $message }}</div>@enderror
    @error('password')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <form wire:submit="login">
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني</label>
            <input type="email" wire:model="email" class="form-control" placeholder="admin@uomtheatre.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label">كلمة المرور</label>
            <input type="password" wire:model="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول</span>
            <span wire:loading><span class="wire-loading"></span> جاري تسجيل الدخول...</span>
        </button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">الحساب الافتراضي: admin@uomtheatre.com / password123</small>
    </div>
</div>
