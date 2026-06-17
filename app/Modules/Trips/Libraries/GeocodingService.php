<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

/**
 * GeocodingService
 *
 * Provides distance calculation between coordinates using Google Distance Matrix API
 * with automatic Haversine fallback. Extracted from duplicated code in controllers.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class GeocodingService
{
    /**
     * Compute distance between two geographic coordinates in kilometers.
     *
     * Attempts Google Distance Matrix API first, falls back to Haversine formula
     * multiplied by 1.3 (road routing approximation).
     *
     * @param float $lat1 Origin latitude
     * @param float $lng1 Origin longitude
     * @param float $lat2 Destination latitude
     * @param float $lng2 Destination longitude
     *
     * @return float Distance in kilometers
     */
    public function getDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $apiKey = env('GoogleMaps.APIKey');

        if (! empty($apiKey)) {
            $distance = $this->_fetchGoogleDistance($lat1, $lng1, $lat2, $lng2, $apiKey);
            if ($distance > 0.0) {
                return $distance;
            }
        }

        // Fallback: Haversine distance * 1.3 (approximation of road routing distance)
        return $this->_calculateHaversineDistance($lat1, $lng1, $lat2, $lng2) * 1.3;
    }

    /**
     * Request distance from Google Distance Matrix API.
     *
     * @param float  $lat1
     * @param float  $lng1
     * @param float  $lat2
     * @param float  $lng2
     * @param string $apiKey
     *
     * @return float Distance in kilometers, or 0.0 on failure
     */
    private function _fetchGoogleDistance(float $lat1, float $lng1, float $lat2, float $lng2, string $apiKey): float
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$lat1},{$lng1}&destinations={$lat2},{$lng2}&key={$apiKey}";

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($url, [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5,
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);

                if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                    // Value returns in meters, convert to Km
                    return (float) $data['rows'][0]['elements'][0]['distance']['value'] / 1000.0;
                }
            }
        } catch (\Throwable $t) {
            log_message('error', 'Google Distance API Request Failed', [
                'exception' => $t->getMessage(),
                'origins'   => "{$lat1},{$lng1}",
                'destinations' => "{$lat2},{$lng2}",
            ]);
        }

        return 0.0;
    }

    /**
     * Calculate straight-line distance using the Haversine formula.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     *
     * @return float Distance in kilometers
     */
    private function _calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}