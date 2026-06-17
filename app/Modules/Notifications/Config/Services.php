<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration for Notifications Module.
 *
 * Registers module-specific services for dependency injection.
 *
 * @package App\Modules\Notifications\Config
 * @author Senior Developer
 * @since 1.0.0
 */
class Services extends BaseService
{
    /**
     * EmailService instance.
     *
     * @param bool $getShared
     *
     * @return \App\Modules\Notifications\Libraries\EmailService
     */
    public static function emailService(bool $getShared = true): \App\Modules\Notifications\Libraries\EmailService
    {
        if ($getShared) {
            return static::getSharedInstance('emailService');
        }

        return new \App\Modules\Notifications\Libraries\EmailService();
    }
}