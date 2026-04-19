<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('إدارة المستخدمين')]
class Users extends Component
{
    public string $successMessage = '';

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $this->successMessage = 'تم ' . ($user->is_active ? 'تفعيل' : 'تعطيل') . ' حساب "' . $user->name . '"';
    }

    public function render()
    {
        if (Auth::user()->role->name !== 'super_admin') return redirect()->route('dashboard');
        return view('livewire.dashboard.users', [
            'users' => User::with('role')->where('role_id', 6)->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
