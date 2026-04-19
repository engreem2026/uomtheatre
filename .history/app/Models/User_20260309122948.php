<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',
        'avatar',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // المستخدم عنده عدة حجوزات
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // المستخدم عنده عدة إشعارات
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ============================================
    // دوال مساعدة للصلاحيات
    // ============================================

    // هل هو مدير نظام؟
    public function isSuperAdmin(): bool
    {
        return $this->role->name === Role::SUPER_ADMIN;
    }

    // هل هو مسؤول فعاليات؟
    public function isEventManager(): bool
    {
        return $this->role->name === Role::EVENT_MANAGER;
    }

    // هل هو مدير مسرح؟
    public function isTheaterManager(): bool
    {
        return $this->role->name === Role::THEATER_MANAGER;
    }

    // هل هو موظف استقبال؟
    public function isReceptionist(): bool
    {
        return $this->role->name === Role::RECEPTIONIST;
    }

    // هل هو من الإدارة (أي دور غير مستخدم عادي)؟
    public function isAdmin(): bool
    {
        return $this->role->name !== Role::USER;
    }

    // هل حسابه فعال؟
    public function isActive(): bool
    {
        return $this->is_active;
    }
}