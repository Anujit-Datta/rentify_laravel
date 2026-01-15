<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureLog extends Model
{
    use HasFactory;

    protected $table = 'signature_logs';

    protected $fillable = [
        'contract_id',
        'user_id',
        'user_role',
        'signature_data',
        'public_key_used',
        'ip_address',
        'device_info',
        'signature_valid',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'signed_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'contract_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}