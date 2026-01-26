<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTerms extends Model
{
    use HasFactory;

    protected $table = 'contract_terms';

    /**
     * Disable automatic timestamps.
     */
    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'monthly_rent',
        'security_deposit',
        'advance_payment',
        'start_date',
        'end_date',
        'duration_months',
        'payment_day',
        'late_fee_per_day',
        'utilities_included',
        'electricity_included',
        'water_included',
        'gas_included',
        'internet_included',
        'maintenance_by',
        'major_repairs_by',
        'pets_allowed',
        'smoking_allowed',
        'subletting_allowed',
        'guests_allowed',
        'max_occupants',
        'tenant_notice_days',
        'landlord_notice_days',
        'special_terms',
        'additional_clauses',
    ];

    protected function casts(): array
    {
        return [
            'monthly_rent' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'advance_payment' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_months' => 'integer',
            'payment_day' => 'integer',
            'late_fee_per_day' => 'decimal:2',
            'electricity_included' => 'boolean',
            'water_included' => 'boolean',
            'gas_included' => 'boolean',
            'internet_included' => 'boolean',
            'pets_allowed' => 'boolean',
            'smoking_allowed' => 'boolean',
            'subletting_allowed' => 'boolean',
        'guests_allowed' => 'boolean',
        'max_occupants' => 'integer',
        'tenant_notice_days' => 'integer',
        'landlord_notice_days' => 'integer',
    ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'contract_id');
    }
}