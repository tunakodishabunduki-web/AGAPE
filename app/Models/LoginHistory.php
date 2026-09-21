<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    public $timestamps = false; // uses created_at only, set explicitly, no updated_at
    protected $fillable = [
        'staff_user_id', 'attempted_username', 'ip_address',
        'user_agent', 'success', 'failure_reason', 'created_at',
    ];
    protected $casts = ['success' => 'boolean', 'created_at' => 'datetime'];

    public function staffUser()
    {
        return $this->belongsTo(StaffUser::class);
    }
}
