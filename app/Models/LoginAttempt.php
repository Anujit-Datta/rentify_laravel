<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';

    protected $fillable = [
        'email',
        'ip_address',
        'success',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'attempt_time' => 'datetime',
        ];
    }
}