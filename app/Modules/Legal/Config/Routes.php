<?php

declare(strict_types=1);

namespace App\Modules\Legal\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('legal', ['namespace' => 'App\Modules\Legal\Controllers'], static function ($routes) {
    $routes->get('terms', 'LegalController::terms', ['as' => 'legal.terms']);
    $routes->get('privacy', 'LegalController::privacy', ['as' => 'legal.privacy']);
});
