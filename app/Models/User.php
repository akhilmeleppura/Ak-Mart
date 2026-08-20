<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
        'user_type',
        'is_supreme_admin',
        'is_super_admin',
        'role_id',
        'branch_id',
        'address_line_1',
        'address_line_2',
        'town',
        'state',
        'post_code',
        'country',
        'locale',
        'role',
        'referral_code',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'is_supreme_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Determine if the user has Supreme Admin privileges.
     */
    public function isSupremeAdmin(): bool
    {
        return (bool) (
            $this->is_supreme_admin ||
            $this->user_type === 'super_admin' ||
            (method_exists($this, 'hasRole') && $this->hasRole('Super Admin'))
        );
    }

    /**
     * Determine if the user has Super Admin or Supreme Admin privileges.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) (
            $this->is_super_admin ||
            $this->is_supreme_admin ||
            $this->user_type === 'super_admin' ||
            (method_exists($this, 'hasRole') && ($this->hasRole('Super Admin') || $this->hasRole('Admin')))
        );
    }

    /**
     * Determine if user is a delivery driver.
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver' || (method_exists($this, 'hasRole') && $this->hasRole('Driver'));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function driverOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}


