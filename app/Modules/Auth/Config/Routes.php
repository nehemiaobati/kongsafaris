<?php

declare(strict_types=1);

namespace App\Modules\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'App\Modules\Auth\Controllers'], static function ($routes) {
    // Login
    $routes->get('login', 'AuthController::loginView', ['as' => 'auth.login']);
    $routes->post('login', 'AuthController::login', ['as' => 'auth.login.submit', 'filter' => 'throttle:5,60']);
    $routes->get('logout', 'AuthController::logout', ['as' => 'auth.logout']);
    $routes->get('dashboard', 'AuthController::dashboard', ['as' => 'auth.dashboard']);

    // Registration & Email Verification
    $routes->get('register', 'AuthController::registerView', ['as' => 'auth.register']);
    $routes->post('register', 'AuthController::register', ['as' => 'auth.register.submit', 'filter' => 'throttle:5,60']);
    $routes->get('register/verify/(:any)', 'AuthController::verifyEmail/$1', ['as' => 'auth.verify']);

    // Password Reset
    $routes->get('forgot-password', 'AuthController::forgotPasswordView', ['as' => 'auth.forgot']);
    $routes->post('forgot-password', 'AuthController::forgotPassword', ['as' => 'auth.forgot.submit', 'filter' => 'throttle:3,60']);
    $routes->get('reset-password/(:any)', 'AuthController::resetPasswordView/$1', ['as' => 'auth.reset']);
    $routes->post('reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'auth.reset.submit', 'filter' => 'throttle:3,60']);

    // Customer Profile
    $routes->get('profile', 'AuthController::profileView', ['as' => 'auth.profile']);
    $routes->post('profile', 'AuthController::profileUpdate', ['as' => 'auth.profile.update']);
});
