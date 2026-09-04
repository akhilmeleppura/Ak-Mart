<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Str;

class AuditLogService
{
    /**
     * Record an immutable audit log entry
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $beforeState = null,
        ?array $afterState = null,
        ?int $actorId = null,
        ?string $actorRole = null
    ): AuditLog {
        $user = auth()->user();
        $actorId = $actorId ?? $user?->id;
        $actorRole = $actorRole ?? ($user ? ($user->user_type ?? 'user') : 'system');

        $ip = request()->ip();
        $userAgent = request()->userAgent();
        $requestId = request()->header('X-Request-ID', (string)Str::uuid());

        return AuditLog::create([
            'user_id'        => $actorId,
            'actor_id'       => $actorId,
            'actor_role'     => $actorRole,
            'event'          => $action,
            'action'         => $action,
            'auditable_type' => $entityType,
            'auditable_id'   => $entityId,
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'old_values'     => self::maskSensitive($beforeState),
            'new_values'     => self::maskSensitive($afterState),
            'ip_address'     => $ip,
            'user_agent'     => $userAgent,
            'request_id'     => $requestId,
        ]);
    }

    /**
     * Mask sensitive fields
     */
    protected static function maskSensitive(?array $data): ?array
    {
        if (!$data) return null;

        $sensitive = ['password', 'otp', 'card_number', 'cvv', 'secret', 'token', 'api_key'];
        foreach ($data as $key => $val) {
            if (is_string($key) && in_array(strtolower($key), $sensitive)) {
                $data[$key] = '********';
            } elseif (is_array($val)) {
                $data[$key] = self::maskSensitive($val);
            }
        }

        return $data;
    }
}
