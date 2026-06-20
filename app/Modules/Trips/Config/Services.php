<?php

declare(strict_types=1);

namespace App\Modules\Trips\Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration for Trips Module.
 *
 * Registers module-specific services for dependency injection.
 *
 * @package App\Modules\Trips\Config
 * @author Senior Developer
 * @since 1.0.0
 */
class Services extends BaseService
{
    /**
     * GeocodingService instance.
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
     */
    public static function tripQueryService(bool $getShared = true): \App\Modules\Trips\Libraries\TripQueryService
    {
        if ($getShared) {
            return static::getSharedInstance('tripQueryService');
        }

        return new \App\Modules\Trips\Libraries\TripQueryService();
    }

    /**
     * BookingService instance.
     */
    public static function bookingService(bool $getShared = true): \App\Modules\Trips\Libraries\BookingService
    {
        if ($getShared) {
            return static::getSharedInstance('bookingService');
        }

        return new \App\Modules\Trips\Libraries\BookingService();
    }

    /**
     * PaymentService instance.
     */
    public static function paymentService(bool $getShared = true): \App\Modules\Trips\Libraries\PaymentService
    {
        if ($getShared) {
            return static::getSharedInstance('paymentService');
        }

        return new \App\Modules\Trips\Libraries\PaymentService();
    }

    /**
     * TrackingService instance.
     */
    public static function trackingService(bool $getShared = true): \App\Modules\Trips\Libraries\TrackingService
    {
        if ($getShared) {
            return static::getSharedInstance('trackingService');
        }

        return new \App\Modules\Trips\Libraries\TrackingService();
    }

    /**
     * QuotationService instance.
     */
    public static function quotationService(bool $getShared = true): \App\Modules\Trips\Libraries\QuotationService
    {
        if ($getShared) {
            return static::getSharedInstance('quotationService');
        }

        return new \App\Modules\Trips\Libraries\QuotationService();
    }

    /**
     * FleetService instance.
     */
    public static function fleetService(bool $getShared = true): \App\Modules\Trips\Libraries\FleetService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetService');
        }

        return new \App\Modules\Trips\Libraries\FleetService();
    }
}