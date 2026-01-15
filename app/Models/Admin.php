<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'security_question',
        'security_answer',
        'is_blocked',
        'failed_attempts',
        'last_login',
        'otp_code',
        'otp_expires_at',
        'is_otp_verified',
        'role',
        'user_id',
    ];

    protected $hidden = [
        'password',
        'security_answer',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'is_blocked' => 'boolean',
            'is_otp_verified' => 'boolean',
            'failed_attempts' => 'integer',
            'last_login' => 'datetime',
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with the admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get activity logs for the admin.
     */
    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class, 'admin_id');
    }

    /**
     * Get allowed IPs for the admin.
     */
    public function allowedIps()
    {
        return $this->hasMany(AdminAllowedIp::class, 'admin_id');
    }
}
