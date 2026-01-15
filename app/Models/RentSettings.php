<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentSettings extends Model
{
    use HasFactory;

    protected $table = 'rent_settings';

    protected $fillable = [
        'property_id',
        'base_rent',
        'include_electricity',
        'electricity_per_unit',
        'electricity_meter_rent',
        'include_water',
        'water_bill',
        'include_gas',
        'gas_bill',
        'include_service',
        'service_charge',
        'include_other',
        'other_charges',
    ];

    protected function casts(): array
    {
        return [
            'base_rent' => 'decimal:2',
            'include_electricity' => 'boolean',
            'electricity_per_unit' => 'decimal:2',
            'electricity_meter_rent' => 'decimal:2',
            'include_water' => 'boolean',
            'water_bill' => 'decimal:2',
            'include_gas' => 'boolean',
            'gas_bill' => 'decimal:2',
            'include_service' => 'boolean',
            'service_charge' => 'decimal:2',
            'include_other' => 'boolean',
            'other_charges' => 'decimal:2',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}