<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalRequest extends Model
{
    use HasFactory;

    protected $table = 'rental_requests';

    protected $fillable = [
        'property_id',
        'unit_id',
        'tenant_id',
        'property_name',
        'tenant_name',
        'tenant_email',
        'tenant_phone',
        'national_id',
        'move_in_date',
        'payment_method',
        'has_pets',
        'current_address',
        'num_occupants',
        'occupation',
        'emergency_contact',
        'emergency_phone',
        'notes',
        'documents',
        'document_path',
        'status',
        'terms',
        'pdf_file',
        'contract_file',
        'contract_id',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'num_occupants' => 'integer',
            'terms' => 'boolean',
            'has_pets' => 'boolean',
            'request_date' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unit()
    {
        return $this->belongsTo(PropertyUnit::class, 'unit_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'contract_id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}