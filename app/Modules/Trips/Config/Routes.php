<?php

declare(strict_types=1);

namespace App\Modules\Trips\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('trips', ['namespace' => 'App\Modules\Trips\Controllers'], static function ($routes) {
    // Quotation and Booking
    $routes->get('quote', 'QuotationController::index', ['as' => 'trips.quote']);
    $routes->post('quote/calculate', 'QuotationController::calculate', ['as' => 'trips.quote.calculate']);

    // Driver Workspace & Real-time updates
    $routes->get('driver', 'TrackingController::driverWorkspace', ['as' => 'trips.driver']);
    $routes->post('driver/status', 'TrackingController::updateTripStatus', ['as' => 'trips.driver.status']);
    $routes->post('tracking/update', 'TrackingController::updateLocation', ['as' => 'trips.tracking.update', 'filter' => 'throttle:1,60']);
    $routes->get('tracking/coordinates/(:num)', 'TrackingController::getCoordinates/$1', ['as' => 'trips.tracking.coordinates']);

    // Customer dashboard and cancellations
    $routes->get('customer', 'QuotationController::customerDashboard', ['as' => 'trips.customer.dashboard']);
    $routes->post('booking/cancel', 'QuotationController::cancelBooking', ['as' => 'trips.booking.cancel']);

    // Manager dashboard, refunds, and fleet CRUD
    $routes->get('manager', 'TrackingController::managerDashboard', ['as' => 'trips.manager']);
    $routes->post('fuel-rate', 'TrackingController::updateFuelRate', ['as' => 'trips.fuel.update']);
    $routes->post('manager/refund', 'TrackingController::processRefund', ['as' => 'trips.manager.refund']);
    $routes->post('manager/assign-driver', 'TrackingController::assignDriver', ['as' => 'trips.manager.assign_driver']);
    
    // Vehicles CRUD
    $routes->post('vehicles/add', 'TrackingController::addVehicle', ['as' => 'trips.vehicle.add']);
    $routes->post('vehicles/edit', 'TrackingController::editVehicle', ['as' => 'trips.vehicle.edit']);
    $routes->post('vehicles/delete/(:num)', 'TrackingController::deleteVehicle/$1', ['as' => 'trips.vehicle.delete']);
    
    // Drivers CRUD
    $routes->post('drivers/add', 'TrackingController::addDriver', ['as' => 'trips.driver.add']);
    $routes->post('drivers/edit', 'TrackingController::editDriver', ['as' => 'trips.driver.edit']);
    $routes->post('drivers/delete/(:num)', 'TrackingController::deleteDriver/$1', ['as' => 'trips.driver.delete']);
});
