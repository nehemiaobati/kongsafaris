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

    // Customer dashboard and cancellations
    $routes->get('customer', 'QuotationController::customerDashboard', ['as' => 'trips.customer.dashboard']);
    $routes->post('booking/cancel', 'QuotationController::cancelBooking', ['as' => 'trips.booking.cancel']);

    // Driver Workspace & Real-time tracking updates
    $routes->get('driver', 'DriverTrackingController::driverWorkspace', ['as' => 'trips.driver']);
    $routes->post('driver/status', 'DriverTrackingController::updateTripStatus', ['as' => 'trips.driver.status']);
    $routes->post('tracking/update', 'DriverTrackingController::updateLocation', ['as' => 'trips.tracking.update', 'filter' => 'throttle:1,60']);
    $routes->get('tracking/coordinates/(:num)', 'DriverTrackingController::getCoordinates/$1', ['as' => 'trips.tracking.coordinates']);

    // Manager dashboard, refunds, fuel rate, and driver assignment
    $routes->get('manager', 'ManagerDashboardController::index', ['as' => 'trips.manager']);
    $routes->post('fuel-rate', 'ManagerDashboardController::updateFuelRate', ['as' => 'trips.fuel.update']);
    $routes->post('manager/refund', 'ManagerDashboardController::processRefund', ['as' => 'trips.manager.refund']);
    $routes->post('manager/assign-driver', 'ManagerDashboardController::assignDriver', ['as' => 'trips.manager.assign_driver']);

    // Manager Admin Override Tools
    $routes->get('manager/manual-booking', 'ManagerDashboardController::manualBookingView', ['as' => 'trips.manager.manual_booking']);
    $routes->post('manager/manual-booking/create', 'ManagerDashboardController::manualBookingCreate', ['as' => 'trips.manager.manual_booking.create']);
    $routes->post('manager/force-cancel', 'ManagerDashboardController::forceCancelBooking', ['as' => 'trips.manager.force_cancel']);
    $routes->post('manager/override-payment', 'ManagerDashboardController::overridePaymentStatus', ['as' => 'trips.manager.override_payment']);
    $routes->post('manager/cancel', 'ManagerDashboardController::cancelBooking', ['as' => 'trips.manager.cancel']);
    $routes->post('manager/initiate-payment', 'ManagerDashboardController::initiatePayment', ['as' => 'trips.manager.initiate_payment']);
    $routes->post('manager/update-settings', 'ManagerDashboardController::updateSystemSettings', ['as' => 'trips.manager.update_settings']);

    // Fleet Vehicles CRUD
    $routes->post('vehicles/add', 'FleetController::addVehicle', ['as' => 'trips.vehicle.add']);
    $routes->post('vehicles/edit', 'FleetController::editVehicle', ['as' => 'trips.vehicle.edit']);
    $routes->post('vehicles/delete/(:num)', 'FleetController::deleteVehicle/$1', ['as' => 'trips.vehicle.delete']);

    // Fleet Drivers CRUD
    $routes->post('drivers/add', 'FleetController::addDriver', ['as' => 'trips.driver.add']);
    $routes->post('drivers/edit', 'FleetController::editDriver', ['as' => 'trips.driver.edit']);
    $routes->post('drivers/delete/(:num)', 'FleetController::deleteDriver/$1', ['as' => 'trips.driver.delete']);

    // Reports
    $routes->get('reports', 'ReportController::index', ['as' => 'trips.reports']);
    $routes->get('reports/csv', 'ReportController::exportCsv', ['as' => 'trips.reports.csv']);
});
