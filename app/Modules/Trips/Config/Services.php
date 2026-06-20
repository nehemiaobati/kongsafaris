<?php

declare(strict_types=1);

namespace App\Modules\Trips\Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration for Trips Module.
 *
 * Registers module-specific services for dependency injection
 * via the Service container.
 *
 * @package App\Modules\Trips\Config
 * @author Senior Developer
 * @since 1.0.0
 */
class Services extends BaseService
{
    /**
     * GeocodingService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Trips\Libraries\GeocodingService
     */
    public static function geocodingService(bool $getShared = true): \App\Modules\Trips\Libraries\GeocodingService
    {
        if ($getShared) {
            return static::getSharedInstance('geocodingService');
        }

        return new \App\Modules\Trips\Libraries\GeocodingService();
    }

    /**
     * PricingService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Trips\Libraries\PricingService
     */
    public static function pricingService(bool $getShared = true): \App\Modules\Trips\Libraries\PricingService
    {
        if ($getShared) {
            return static::getSharedInstance('pricingService');
        }

        return new \App\Modules\Trips\Libraries\PricingService();
    }

    /**
     * TripQueryService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Trips\Libraries\TripQueryService
     */
    public static function tripQueryService(bool $getShared = true): \App\Modules\Trips\Libraries\TripQueryService
    {
        if ($getShared) {
            return static::getSharedInstance('tripQueryService');
        }

        return new \App\Modules\Trips\Libraries\TripQueryService();
    }
}
