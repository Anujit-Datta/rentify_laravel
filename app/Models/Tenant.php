<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tenants';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'location',
        'budget',
        'bio',
        'profile_pic',
        'gender',
        'age',
        'address',
        'preferred_location',
        'budget_min',
        'budget_max',
        'preferred_property_type',
        'move_in_date',
        'family_size',
        'occupation',
        'hobby',
        'pet',
        'smoker',
        'employed',
        'emergency_contact_name',
        'emergency_contact_phone',
        'user_id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'budget_min' => 'integer',
            'budget_max' => 'integer',
            'age' => 'integer',
            'family_size' => 'integer',
            'move_in_date' => 'date',
            'smoker' => 'boolean',
            'employed' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with the tenant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get rental requests for the tenant.
     */
    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class, 'tenant_id');
    }

    /**
     * Get rentals for the tenant.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'tenant_id');
    }

    /**
     * Get contracts for the tenant.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'tenant_id');
    }

    /**
     * Get property reviews by the tenant.
     */
    public function propertyReviews()
    {
        return $this->hasMany(PropertyReview::class, 'tenant_id');
    }

    /**
     * Get reviews about the tenant.
     */
    public function reviewsAbout()
    {
        return $this->hasMany(TenantReview::class, 'tenant_id');
    }

    /**
     * Get favourites for the tenant.
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'tenant_id');
    }

    /**
     * Get payments made by the tenant.
     */
    public function payments()
    {
        return $this->hasMany(RentPayment::class, 'tenant_id');
    }

    /**
     * Get receipts for the tenant.
     */
    public function receipts()
    {
        return $this->hasMany(RentReceipt::class, 'tenant_id');
    }
}
