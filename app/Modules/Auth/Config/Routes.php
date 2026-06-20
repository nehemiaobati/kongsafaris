<?php

declare(strict_types=1);

namespace App\Modules\Auth\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('auth', ['namespace' => 'App\Modules\Auth\Controllers'], static function ($routes) {
    // Login (public)
    $routes->get('login', 'AuthController::loginView', ['as' => 'auth.login']);
    $routes->post('login', 'AuthController::login', ['as' => 'auth.login.submit', 'filter' => 'throttle:1,60']);
    $routes->get('logout', 'AuthController::logout', ['as' => 'auth.logout']);

    // Admin User Management (admin role only)
    $routes->get('admin/users', 'AdminController::users', ['as' => 'auth.admin.users', 'filter' => 'auth:admin']);
    $routes->get('admin/users/create', 'AdminController::createUserView', ['as' => 'auth.admin.create_user', 'filter' => 'auth:admin']);
    $routes->post('admin/users/create', 'AdminController::createUser', ['as' => 'auth.admin.create_user.submit', 'filter' => 'auth:admin']);
    $routes->get('admin/users/edit/(:num)', 'AdminController::editUserView/$1', ['as' => 'auth.admin.edit_user', 'filter' => 'auth:admin']);
    $routes->post('admin/users/edit/(:num)', 'AdminController::editUser/$1', ['as' => 'auth.admin.edit_user.submit', 'filter' => 'auth:admin']);
    $routes->post('admin/users/delete/(:num)', 'AdminController::deleteUser/$1', ['as' => 'auth.admin.delete_user', 'filter' => 'auth:admin']);

    // Dashboard (any authenticated user)
    $routes->get('dashboard', 'AuthController::dashboard', ['as' => 'auth.dashboard', 'filter' => 'auth']);

    // Registration & Email Verification (public)
    $routes->get('register', 'AuthController::registerView', ['as' => 'auth.register']);
    $routes->post('register', 'AuthController::register', ['as' => 'auth.register.submit', 'filter' => 'throttle:5,60']);
    $routes->get('register/verify/(:any)', 'AuthController::verifyEmail/$1', ['as' => 'auth.verify']);

    // Password Reset (public with token)
    $routes->get('forgot-password', 'AuthController::forgotPasswordView', ['as' => 'auth.forgot']);
    $routes->post('forgot-password', 'AuthController::forgotPassword', ['as' => 'auth.forgot.submit', 'filter' => 'throttle:3,60']);
    $routes->get('reset-password/(:any)', 'AuthController::resetPasswordView/$1', ['as' => 'auth.reset']);
    $routes->post('reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'auth.reset.submit', 'filter' => 'throttle:3,60']);

    // Customer Profile (any authenticated user)
    $routes->get('profile', 'AuthController::profileView', ['as' => 'auth.profile', 'filter' => 'auth']);
    $routes->post('profile', 'AuthController::profileUpdate', ['as' => 'auth.profile.update', 'filter' => 'auth']);
});
