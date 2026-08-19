<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerification extends Model
{
    protected $fillable = [
        'user_id',
        'identifier',
        'purpose',
        'otp_hash',
        'session_token',
        'expires_at',
        'verified_at',
        'attempts',
        'max_attempts',
        'resend_count',
        'max_resends',
        'last_sent_at',
        'ip_address',
        'user_agent',
        'is_invalidated',
    ];

    protected $casts = [
        'expires_at'     => 'datetime',
        'verified_at'    => 'datetime',
        'last_sent_at'   => 'datetime',
        'is_invalidated' => 'boolean',
        'attempts'       => 'integer',
        'max_attempts'   => 'integer',
        'resend_count'   => 'integer',
        'max_resends'    => 'integer',
    ];

    // -----------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -----------------------------------------------------------------
    // Computed / Helpers
    // -----------------------------------------------------------------

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isInvalidated(): bool
    {
        return $this->is_invalidated;
    }

    public function isUsable(): bool
    {
        return ! $this->is_invalidated
            && ! $this->isExpired()
            && ! $this->isVerified()
            && $this->attempts < $this->max_attempts;
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= $this->max_attempts;
    }

    public function hasExceededResends(): bool
    {
        return $this->resend_count >= $this->max_resends;
    }

    public function canResend(int $cooldownSeconds): bool
    {
        if ($this->hasExceededResends()) {
            return false;
        }

        if ($this->last_sent_at === null) {
            return true;
        }

        return $this->last_sent_at->addSeconds($cooldownSeconds)->isPast();
    }

    public function secondsUntilResend(int $cooldownSeconds): int
    {
        if ($this->last_sent_at === null) {
            return 0;
        }

        $diff = now()->diffInSeconds($this->last_sent_at->addSeconds($cooldownSeconds), false);

        return max(0, (int) $diff);
    }

    // -----------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------

    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeActive($query)
    {
        return $query
            ->where('is_invalidated', false)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now());
    }

    public function scopeForIdentifier($query, string $identifier)
    {
        return $query->where('identifier', $identifier);
    }
}
