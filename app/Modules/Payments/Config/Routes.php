<?php

declare(strict_types=1);

namespace App\Modules\Payments\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('payments', ['namespace' => 'App\Modules\Payments\Controllers'], static function ($routes) {
    $routes->post('checkout', 'PaystackController::checkout', ['as' => 'payments.checkout', 'filter' => 'throttle:10,60']);
    $routes->get('callback', 'PaystackController::callback', ['as' => 'payments.callback']);
    $routes->post('webhook', 'PaystackController::webhook', ['as' => 'payments.webhook', 'filter' => 'throttle:20,60']);
});
