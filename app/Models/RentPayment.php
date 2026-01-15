<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    use HasFactory;

    protected $table = 'rent_payments';

    protected $fillable = [
        'rental_id',
        'tenant_id',
        'landlord_id',
        'property_id',
        'amount',
        'payment_month',
        'payment_date',
        'status',
        'payment_method',
        'is_advance',
        'note',
        'transaction_id',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_month' => 'date',
            'payment_date' => 'date',
            'is_advance' => 'boolean',
            'confirmed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
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

    public function receipt()
    {
        return $this->hasOne(RentReceipt::class, 'payment_id');
    }
}