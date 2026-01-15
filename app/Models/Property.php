<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    /**
     * Disable automatic timestamps.
     * The database table doesn't have created_at and updated_at columns.
     */
    public $timestamps = false;

    protected $fillable = [
        'property_name',
        'location',
        'rent',
        'landlord',
        'image',
        'bedrooms',
        'property_type',
        'description',
        'available',
        'posted_date',
        'featured',
        'size',
        'bathrooms',
        'floor',
        'parking',
        'furnished',
        'map_embed',
        'floor_plan',
        'latitude',
        'longitude',
        'status',
        'rental_type',
        'is_building',
        'total_floors',
        'thumbnail',
        'landlord_id',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'rent' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'total_floors' => 'integer',
            'available' => 'boolean',
            'featured' => 'boolean',
            'parking' => 'boolean',
            'furnished' => 'boolean',
            'is_building' => 'boolean',
            'is_verified' => 'boolean',
            'posted_date' => 'date',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the landlord (user) that owns the property.
     */
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the floors for the property.
     */
    public function floors()
    {
        return $this->hasMany(PropertyFloor::class);
    }

    /**
     * Get the units for the property.
     */
    public function units()
    {
        return $this->hasMany(PropertyUnit::class);
    }

    /**
     * Get the gallery images for the property.
     */
    public function gallery()
    {
        return $this->hasMany(PropertyGallery::class);
    }

    /**
     * Get the amenities for the property.
     */
    public function amenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    /**
     * Get the reports for the property.
     */
    public function reports()
    {
        return $this->hasMany(PropertyReport::class);
    }

    /**
     * Get the reviews for the property.
     */
    public function reviews()
    {
        return $this->hasMany(PropertyReview::class);
    }

    /**
     * Get the rental requests for the property.
     */
    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    /**
     * Get the rentals for the property.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get the contracts for the property.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get rent settings for the property.
     */
    public function rentSettings()
    {
        return $this->hasOne(RentSettings::class);
    }

    /**
     * Get the favourites for the property.
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Get the payments for the property.
     */
    public function payments()
    {
        return $this->hasMany(RentPayment::class);
    }

    /**
     * Scope a query to only include available properties.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', 1);
    }

    /**
     * Scope a query to only include featured properties.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }

    /**
     * Scope a query to filter by rental type.
     */
    public function scopeRentalType($query, $type)
    {
        return $query->where('rental_type', $type);
    }

    /**
     * Scope a query to search by location.
     */
    public function scopeSearchLocation($query, $search)
    {
        return $query->where('location', 'LIKE', "%{$search}%");
    }

    /**
     * Scope a query to filter by rent range.
     */
    public function scopeRentRange($query, $min, $max)
    {
        return $query->whereBetween('rent', [$min, $max]);
    }
}
