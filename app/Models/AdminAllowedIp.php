<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAllowedIp extends Model
{
    use HasFactory;

    protected $table = 'admin_allowed_ips';

    protected $fillable = [
        'ip_address',
        'description',
        'admin_id',
        'added_by',
        'is_active',
        'last_used',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'last_used' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}