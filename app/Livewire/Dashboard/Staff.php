<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('إدارة الموظفين')]
class Staff extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $phone = '';
    public int $role_id = 0;
    public int $editId = 0;
    public string $editName = '';
    public string $editEmail = '';
    public string $editPassword = '';
    public string $editPhone = '';
    public int $editRoleId = 0;
    public string $successMessage = '';

    public function createStaff()
    {
        $this->validate([
            'name'=>'required|string|max:255','email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6','phone'=>'nullable|string','role_id'=>'required|exists:roles,id',
        ],['name.required'=>'الاسم مطلوب','email.required'=>'البريد مطلوب','email.unique'=>'البريد مستخدم','password.required'=>'كلمة المرور مطلوبة','password.min'=>'6 أحرف على الأقل','role_id.required'=>'يجب اختيار الدور']);

        User::create(['name'=>$this->name,'email'=>$this->email,'password'=>$this->password,'phone'=>$this->phone,'role_id'=>$this->role_id]);
        $this->successMessage = 'تم إنشاء الموظف "' . $this->name . '" بنجاح';
        $this->reset(['name','email','password','phone','role_id']);
        $this->dispatch('close-modal');
    }

    public function openEdit(int $id)
    {
        $user = User::findOrFail($id);
        $this->editId=$user->id; $this->editName=$user->name; $this->editEmail=$user->email;
        $this->editPhone=$user->phone??''; $this->editRoleId=$user->role_id; $this->editPassword='';
    }

    public function updateStaff()
    {
        $this->validate(['editName'=>'required|string|max:255','editEmail'=>'required|email|unique:users,email,'.$this->editId,'editRoleId'=>'required|exists:roles,id']);
        $user = User::findOrFail($this->editId);
        $data = ['name'=>$this->editName,'email'=>$this->editEmail,'phone'=>$this->editPhone,'role_id'=>$this->editRoleId];
        if (!empty($this->editPassword)) $data['password'] = $this->editPassword;
        $user->update($data);
        $this->successMessage = 'تم تعديل بيانات "' . $user->name . '" بنجاح';
        $this->dispatch('close-modal');
    }

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id == Auth::id()) { $this->addError('toggle','لا يمكنك تعطيل حسابك'); return; }
        $user->update(['is_active' => !$user->is_active]);
        $this->successMessage = 'تم ' . ($user->is_active ? 'تفعيل' : 'تعطيل') . ' حساب "' . $user->name . '"';
    }

    public function render()
    {
        if (Auth::user()->role->name !== 'super_admin') return redirect()->route('dashboard');
        return view('livewire.dashboard.staff', [
            'staff' => User::with('role')->where('role_id','!=',6)->orderBy('created_at','desc')->get(),
            'roles' => Role::where('id','!=',6)->get(),
        ]);
    }
}
