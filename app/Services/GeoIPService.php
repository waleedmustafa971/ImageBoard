<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoIPService
{
    /**
     * Get country information from IP address
     * Uses ip-api.com free service (limited to 45 requests per minute)
     */
    public function getCountryFromIP(string $ipAddress): array
    {
        // Return default for localhost/private IPs
        if ($this->isLocalOrPrivateIP($ipAddress)) {
            return [
                'country_code' => 'XX',
                'country_name' => 'Unknown',
            ];
        }

        // Check cache first (cache for 24 hours)
        $cacheKey = 'geoip_' . $ipAddress;

        return Cache::remember($cacheKey, 86400, function () use ($ipAddress) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}", [
                    'fields' => 'status,countryCode,country',
                ]);

                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'country_code' => $response->json('countryCode'),
                        'country_name' => $response->json('country'),
                    ];
                }
            } catch (\Exception $e) {
                // Silently fail and return unknown
            }

            return [
                'country_code' => 'XX',
                'country_name' => 'Unknown',
            ];
        });
    }

    /**
     * Check if IP is localhost or private
     */
    protected function isLocalOrPrivateIP(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') {
            return true;
        }

        // Check if private IP
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
