<?php

declare(strict_types=1);

namespace App\Modules\Auth\Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration for Auth Module.
 *
 * Registers module-specific services for dependency injection.
 *
 * @package App\Modules\Auth\Config
 * @author Senior Developer
 * @since 1.0.0
 */
class Services extends BaseService
{
    /**
     * AuthService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Auth\Libraries\AuthService
     */
    public static function authService(bool $getShared = true): \App\Modules\Auth\Libraries\AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }

        return new \App\Modules\Auth\Libraries\AuthService();
    }
}
