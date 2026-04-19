<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public string $errorMessage = '';

    public function login()
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'البريد مطلوب',
            'email.email'       => 'البريد غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $this->errorMessage = 'حسابك معطّل. تواصل مع الإدارة';
                return;
            }

            if ($user->role->name === 'user') {
                Auth::logout();
                $this->errorMessage = 'لوحة التحكم مخصصة للموظفين. استخدم التطبيق للحجز.';
                return;
            }

            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $this->errorMessage = 'البريد أو كلمة المرور غير صحيحة';
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
