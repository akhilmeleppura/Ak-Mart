<?php

namespace App\Services\Ai;

class PromptSecurityGuard
{
    /**
     * List of malicious prompt injection patterns
     */
    protected static array $injectionPatterns = [
        '/ignore\s+(all\s+)?(previous|prior)\s+instructions/i',
        '/bypass\s+(security|authorization|rules)/i',
        '/(dump|show|give|leak|export)\s+(all\s+)?(passwords|hashes|secrets|credentials|tokens|api_keys|env)/i',
        '/(delete|drop|truncate|alter)\s+(database|tables|products|orders|users)/i',
        '/system\s+prompt\s+override/i',
        '/act\s+as\s+(an\s+unrestricted|a\s+hacked)\s+(ai|terminal|bot)/i',
        '/(sql\s+injection|select\s+\*\s+from\s+users)/i',
    ];

    /**
     * Inspect prompt for malicious injection or unauthorized exfiltration attempts.
     */
    public static function inspect(string $prompt): array
    {
        foreach (self::$injectionPatterns as $pattern) {
            if (preg_match($pattern, $prompt)) {
                return [
                    'safe'   => false,
                    'reason' => 'Potential prompt injection or security violation detected.',
                ];
            }
        }

        return ['safe' => true];
    }

    /**
     * Mask sensitive PII and secrets from AI inputs/outputs.
     */
    public static function maskSensitiveData(string $text): string
    {
        // Mask passwords / api keys if present
        $text = preg_replace('/(password|secret|api_key|token)\s*[:=]\s*([^\s,]+)/i', '$1: [REDACTED]', $text);
        
        // Mask full credit card numbers
        $text = preg_replace('/\b(?:\d[ -]*?){13,16}\b/', '[CARD-REDACTED]', $text);

        return $text;
    }
}
