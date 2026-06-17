<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('notifications', ['namespace' => 'App\Modules\Notifications\Controllers'], static function ($routes) {
    $routes->get('/', 'NotificationsController::index', ['as' => 'notifications.index']);
});
