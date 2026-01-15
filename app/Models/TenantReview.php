<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantReview extends Model
{
    use HasFactory;

    protected $table = 'tenant_reviews';

    protected $fillable = [
        'tenant_id',
        'landlord_id',
        'rating',
        'review_text',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tenantUser()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    public function landlordUser()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }
}