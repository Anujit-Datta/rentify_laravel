<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractVerification extends Model
{
    use HasFactory;

    protected $table = 'contract_verifications';

    protected $fillable = [
        'contract_id',
        'verification_result',
        'verification_type',
        'hash_match',
        'tenant_signature_valid',
        'landlord_signature_valid',
        'ip_address',
        'user_agent',
        'verified_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'hash_match' => 'boolean',
            'tenant_signature_valid' => 'boolean',
            'landlord_signature_valid' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'contract_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}