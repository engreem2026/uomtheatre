<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        $user = User::with('role')->where('email', $this->email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->errorMessage = 'البريد أو كلمة المرور غير صحيحة';
            return;
        }

        if (!$user->is_active) {
            $this->errorMessage = 'حسابك معطّل. تواصل مع الإدارة';
            return;
        }

        if (!$user->isAdmin()) {
            $this->errorMessage = 'لوحة التحكم مخصصة للموظفين. استخدم التطبيق للحجز.';
            return;
        }

        session([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'user_email' => $user->email,
            'user_role'  => $user->role->display_name,
            'role_name'  => $user->role->name,
        ]);

        return redirect()->route('dashboard')->with('success', 'مرحباً ' . $user->name);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
