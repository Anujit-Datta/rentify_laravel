<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    use HasFactory;

    protected $table = 'wallet_balances';

    protected $fillable = [
        'user_id',
        'balance',
        'monthly_added',
        'last_reset_date',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'monthly_added' => 'decimal:2',
            'last_reset_date' => 'date',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}