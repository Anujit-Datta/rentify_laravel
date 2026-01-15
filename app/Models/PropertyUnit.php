<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyUnit extends Model
{
    use HasFactory;

    protected $table = 'property_units';

    protected $fillable = [
        'floor_id',
        'property_id',
        'unit_name',
        'unit_number',
        'rent',
        'bedrooms',
        'bathrooms',
        'size',
        'rental_type',
        'is_sublet',
        'max_tenants',
        'current_tenants',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'rent' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'is_sublet' => 'boolean',
            'max_tenants' => 'integer',
            'current_tenants' => 'integer',
            'is_available' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the property that owns the unit.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the floor that owns the unit.
     */
    public function floor()
    {
        return $this->belongsTo(PropertyFloor::class, 'floor_id');
    }

    /**
     * Get the rental requests for the unit.
     */
    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class, 'unit_id');
    }

    /**
     * Scope a query to only include available units.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    /**
     * Scope a query to only include sublet units.
     */
    public function scopeSublet($query)
    {
        return $query->where('is_sublet', 1);
    }
}
