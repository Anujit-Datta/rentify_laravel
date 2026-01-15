<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    /**
     * Disable automatic timestamps.
     * The database table doesn't have created_at and updated_at columns.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phoneVerified',
        'is_verified',
        'role',
        'nid_number',
        'nid_front',
        'nid_back',
        'is_landlord_verified',
        'status',
        'verification_code',
        'reset_token',
        'reset_expiry',
        'public_key',
        'private_key',
        'key_created',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'private_key',
        'reset_token',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phoneVerified' => 'boolean',
            'is_verified' => 'boolean',
            'is_landlord_verified' => 'boolean',
            'key_created' => 'boolean',
            'reset_expiry' => 'datetime',
            'last_seen' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant associated with the user.
     */
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * Get the landlord associated with the user.
     */
    public function landlord()
    {
        return $this->hasOne(Landlord::class);
    }

    /**
     * Get the admin associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get the properties for the user (landlord).
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    /**
     * Get the rental requests for the user (tenant).
     */
    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class, 'tenant_id');
    }

    /**
     * Get the rentals for the user (tenant).
     */
    public function rentalsAsTenant()
    {
        return $this->hasMany(Rental::class, 'tenant_id');
    }

    /**
     * Get the rentals where user is landlord.
     */
    public function rentalsAsLandlord()
    {
        return $this->hasMany(Rental::class, 'landlord_id');
    }

    /**
     * Get contracts where user is tenant.
     */
    public function contractsAsTenant()
    {
        return $this->hasMany(Contract::class, 'tenant_id');
    }

    /**
     * Get contracts where user is landlord.
     */
    public function contractsAsLandlord()
    {
        return $this->hasMany(Contract::class, 'landlord_id');
    }

    /**
     * Get sent messages.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get received messages.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get support tickets for the user.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    /**
     * Get favourites for the user (tenant).
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'tenant_id');
    }

    /**
     * Get payments made by user (tenant).
     */
    public function paymentsMade()
    {
        return $this->hasMany(RentPayment::class, 'tenant_id');
    }

    /**
     * Get payments received by user (landlord).
     */
    public function paymentsReceived()
    {
        return $this->hasMany(RentPayment::class, 'landlord_id');
    }

    /**
     * Get wallet balance for the user.
     */
    public function walletBalance()
    {
        return $this->hasOne(WalletBalance::class);
    }

    /**
     * Get wallet transactions for the user.
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Users blocked by this user.
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'blocker_id', 'blocked_id')
                    ->withTimestamps()
                    ->withPivot('reason', 'blocked_at');
    }

    /**
     * Users who blocked this user.
     */
    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'blocked_id', 'blocker_id')
                    ->withTimestamps()
                    ->withPivot('reason', 'blocked_at');
    }

    /**
     * Reports made by this user.
     */
    public function reportsMade()
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    /**
     * Reports about this user.
     */
    public function reportsReceived()
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
    }

    /**
     * Login attempts for this user.
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'email', 'email');
    }

    /**
     * Security logs for this user.
     */
    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }

    /**
     * Action logs for this user.
     */
    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class);
    }

    /**
     * Signature logs for this user.
     */
    public function signatureLogs()
    {
        return $this->hasMany(SignatureLog::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is tenant.
     */
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Check if user is landlord.
     */
    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    /**
     * Get property reviews by user.
     */
    public function propertyReviews()
    {
        return $this->hasMany(PropertyReview::class, 'tenant_id');
    }

    /**
     * Get tenant reviews about user.
     */
    public function tenantReviewsAbout()
    {
        return $this->hasMany(TenantReview::class, 'tenant_id');
    }

    /**
     * Get tenant reviews by user (landlord).
     */
    public function tenantReviewsBy()
    {
        return $this->hasMany(TenantReview::class, 'landlord_id');
    }
}
