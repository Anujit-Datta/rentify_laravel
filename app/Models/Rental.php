<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rentals';

    /**
     * Disable automatic timestamps.
     */
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'landlord_id',
        'tenant_id',
        'start_date',
        'end_date',
        'status',
        'contract_id',
        'contract_file',
        'tenant_signed',
        'landlord_signed',
        'tenant_signature',
        'landlord_signature',
        'verified',
        'contract_hash',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'tenant_signed' => 'boolean',
            'landlord_signed' => 'boolean',
            'verified' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function payments()
    {
        return $this->hasMany(RentPayment::class, 'rental_id');
    }
}