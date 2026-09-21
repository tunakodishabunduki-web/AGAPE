<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StaffUser extends Authenticatable
{
    protected $fillable = [
        'username', 'password', 'role', 'totp_secret', 'totp_pending_secret',
        'totp_enabled', 'must_change_password', 'last_login_at',
    ];

    protected $hidden = ['password', 'totp_secret', 'totp_pending_secret'];

    protected $casts = [
        'totp_enabled' => 'boolean',
        'must_change_password' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
