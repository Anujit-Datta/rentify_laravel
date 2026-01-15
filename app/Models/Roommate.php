<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roommate extends Model
{
    use HasFactory;

    protected $table = 'roommates';

    protected $fillable = [
        'name',
        'gender',
        'age',
        'location',
        'budget',
        'occupation',
        'smoking',
        'pets',
        'cleanliness',
        'about',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'budget' => 'integer',
        ];
    }

    public function scopeGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeBudgetRange($query, $min, $max)
    {
        return $query->whereBetween('budget', [$min, $max]);
    }
}