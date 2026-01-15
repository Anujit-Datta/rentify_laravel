<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    use HasFactory;

    protected $table = 'landlords';

    protected $fillable = [
        'full_name',
        'gender',
        'age',
        'occupation',
        'experience',
        'about',
        'profile_pic',
        'hobby',
        'pet',
        'user_id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
        ];
    }

    /**
     * Get the user associated with the landlord.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get properties for the landlord.
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    /**
     * Get rentals where user is landlord.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'landlord_id');
    }

    /**
     * Get contracts where user is landlord.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'landlord_id');
    }

    /**
     * Get tenant reviews by the landlord.
     */
    public function tenantReviewsBy()
    {
        return $this->hasMany(TenantReview::class, 'landlord_id');
    }

    /**
     * Get payments received by the landlord.
     */
    public function payments()
    {
        return $this->hasMany(RentPayment::class, 'landlord_id');
    }

    /**
     * Get receipts for payments received.
     */
    public function receipts()
    {
        return $this->hasMany(RentReceipt::class, 'landlord_id');
    }
}
