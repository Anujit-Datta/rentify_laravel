<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $fillable = [
        'contract_id',
        'tenant_id',
        'landlord_id',
        'property_id',
        'rental_request_id',
        'contract_data',
        'contract_hash',
        'pdf_filename',
        'pdf_filepath',
        'tenant_signature',
        'tenant_signed_at',
        'landlord_signature',
        'landlord_signed_at',
        'verification_url',
        'qr_code_data',
        'status',
        'activated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'contract_data' => 'array',
            'tenant_signed_at' => 'datetime',
            'landlord_signed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'activated_at' => 'datetime',
            'expires_at' => 'date',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function terms()
    {
        return $this->hasOne(ContractTerms::class, 'contract_id', 'contract_id');
    }

    public function verifications()
    {
        return $this->hasMany(ContractVerification::class, 'contract_id', 'contract_id');
    }

    public function signatureLogs()
    {
        return $this->hasMany(SignatureLog::class, 'contract_id', 'contract_id');
    }
}