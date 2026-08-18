<?php

namespace App\Services;

class SsrfProtectionService
{
    /**
     * Blacklisted IP ranges and hosts
     */
    protected array $blockedHosts = [
        'localhost',
        '127.0.0.1',
        '0.0.0.0',
        '::1',
        'metadata.google.internal',
        '169.254.169.254',
    ];

    /**
     * Validate if the target URL is safe from SSRF exploits.
     *
     * @param string $url
     * @return array ['safe' => bool, 'message' => string, 'ip' => ?string]
     */
    public function validateUrl(string $url): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['safe' => false, 'message' => 'Invalid URL format.', 'ip' => null];
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $port = $parts['port'] ?? null;

        // 1. Only allow http and https
        if (!in_array($scheme, ['http', 'https'])) {
            return ['safe' => false, 'message' => 'Only HTTP and HTTPS protocols are permitted.', 'ip' => null];
        }

        // 2. Check port
        if ($port !== null && !in_array($port, [80, 443])) {
            return ['safe' => false, 'message' => 'Only standard HTTP/HTTPS ports (80, 443) are allowed.', 'ip' => null];
        }

        // 3. Check blocked hostnames
        if (in_array($host, $this->blockedHosts) || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) {
            return ['safe' => false, 'message' => 'Access to internal hostnames is prohibited.', 'ip' => null];
        }

        // 4. Resolve DNS and validate IP address
        $ip = gethostbyname($host);
        if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            // DNS resolution failed or invalid host
            return ['safe' => false, 'message' => 'Could not resolve domain name.', 'ip' => null];
        }

        if ($this->isPrivateOrReservedIp($ip)) {
            return ['safe' => false, 'message' => "Access to private or link-local IP addresses ({$ip}) is strictly blocked.", 'ip' => $ip];
        }

        return ['safe' => true, 'message' => 'URL verified safe.', 'ip' => $ip];
    }

    /**
     * Check if an IP address belongs to a private, loopback, or reserved range.
     */
    public function isPrivateOrReservedIp(string $ip): bool
    {
        // Use PHP filter flags for private and reserved ranges
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        if (!filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
            return true;
        }

        // Extra check for AWS/GCP metadata service
        if ($ip === '169.254.169.254') {
            return true;
        }

        return false;
    }
}
