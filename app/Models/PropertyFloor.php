<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFloor extends Model
{
    use HasFactory;

    protected $table = 'property_floors';

    protected $fillable = [
        'property_id',
        'floor_number',
        'total_units',
    ];

    protected function casts(): array
    {
        return [
            'floor_number' => 'integer',
            'total_units' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function units()
    {
        return $this->hasMany(PropertyUnit::class, 'floor_id');
    }
}