<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 管理员用户模型（admin_users）
 *
 * Purpose: authenticate Filament admin panel using existing admin_users table.
 */
class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_users';

    protected $fillable = [
        'name', 'username', 'email', 'password', 'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 允许访问后台面板
     *
     * Purpose: gate Filament admin access; here allow all authenticated admins.
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true;
    }
}