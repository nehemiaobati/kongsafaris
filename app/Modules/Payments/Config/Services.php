<?php

declare(strict_types=1);

namespace App\Modules\Payments\Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration for Payments Module.
 *
 * Registers module-specific services for dependency injection.
 *
 * @package App\Modules\Payments\Config
 * @author Senior Developer
 * @since 1.0.0
 */
class Services extends BaseService
{
    /**
     * PaystackService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Payments\Libraries\PaystackService
     */
    public static function paystackService(bool $getShared = true): \App\Modules\Payments\Libraries\PaystackService
    {
        if ($getShared) {
            return static::getSharedInstance('paystackService');
        }

        return new \App\Modules\Payments\Libraries\PaystackService();
    }
}