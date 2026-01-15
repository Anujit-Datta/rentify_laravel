<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentReceipt extends Model
{
    use HasFactory;

    protected $table = 'rent_receipts';

    protected $fillable = [
        'payment_id',
        'receipt_number',
        'tenant_id',
        'landlord_id',
        'rent_amount',
        'late_fee',
        'total_amount',
        'payment_month',
        'pdf_path',
        'email_sent',
    ];

    protected function casts(): array
    {
        return [
            'rent_amount' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'payment_month' => 'date',
            'email_sent' => 'boolean',
            'generated_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(RentPayment::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }
}