<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    // Disable timestamps - table uses 'timestamp' column instead
    public $timestamps = false;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'file_path',
        'receiver_role',
        'priority',
        'sender_role',
        'status',
        'seen',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seen' => 'boolean',
        'timestamp' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}