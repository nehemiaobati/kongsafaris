<?php

declare(strict_types=1);

namespace App\Modules\Trips\Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('trips', ['namespace' => 'App\Modules\Trips\Controllers'], static function ($routes) {
    // Quotation and Booking (public)
    $routes->get('quote', 'QuotationController::index', ['as' => 'trips.quote']);
    $routes->post('quote/calculate', 'QuotationController::calculate', ['as' => 'trips.quote.calculate']);
    $routes->post('geocode/reverse', 'QuotationController::reverseGeocode', ['as' => 'trips.geocode.reverse']);

    // Customer dashboard and cancellations (customer role)
    $routes->get('customer', 'QuotationController::customerDashboard', ['as' => 'trips.customer.dashboard', 'filter' => 'auth:customer']);
    $routes->post('booking/cancel', 'QuotationController::cancelBooking', ['as' => 'trips.booking.cancel', 'filter' => 'auth:customer']);

    // Driver Workspace & Real-time tracking updates (driver role)
    $routes->get('driver', 'DriverTrackingController::driverWorkspace', ['as' => 'trips.driver', 'filter' => 'auth:driver']);
    $routes->post('driver/status', 'DriverTrackingController::updateTripStatus', ['as' => 'trips.driver.status', 'filter' => 'auth:driver']);
    $routes->post('tracking/update', 'DriverTrackingController::updateLocation', ['as' => 'trips.tracking.update', 'filter' => 'auth:driver,throttle:1,60']);
    $routes->get('tracking/coordinates/(:num)', 'DriverTrackingController::getCoordinates/$1', ['as' => 'trips.tracking.coordinates', 'filter' => 'auth:driver,manager,admin']);

    // Manager dashboard (manager/admin role)
    $routes->get('manager', 'ManagerDashboardController::index', ['as' => 'trips.manager', 'filter' => 'auth:manager,admin']);
    $routes->post('fuel-rate', 'ManagerDashboardController::updateFuelRate', ['as' => 'trips.fuel.update', 'filter' => 'auth:manager,admin']);

    // Booking operations via BookingController (manager/admin role)
    $routes->post('manager/assign-driver', 'ManagerDashboardController::assignDriver', ['as' => 'trips.manager.assign_driver', 'filter' => 'auth:manager,admin']);
    $routes->get('manager/manual-booking', 'ManagerDashboardController::manualBookingView', ['as' => 'trips.manager.manual_booking', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/manual-booking/create', 'BookingController::manualBookingCreate', ['as' => 'trips.manager.manual_booking.create', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/force-cancel', 'BookingController::forceCancelBooking', ['as' => 'trips.manager.force_cancel', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/cancel', 'BookingController::cancelBooking', ['as' => 'trips.manager.cancel', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/update-booking', 'BookingController::updateBooking', ['as' => 'trips.manager.update_booking', 'filter' => 'auth:manager,admin']);

    // Payment operations via PaymentController (manager/admin role)
    $routes->post('manager/refund', 'PaymentController::processRefund', ['as' => 'trips.manager.refund', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/initiate-payment', 'PaymentController::initiatePayment', ['as' => 'trips.manager.initiate_payment', 'filter' => 'auth:manager,admin']);
    $routes->post('manager/override-payment', 'PaymentController::overridePaymentStatus', ['as' => 'trips.manager.override_payment', 'filter' => 'auth:manager,admin']);

    // System settings (manager/admin role)
    $routes->post('manager/update-settings', 'SystemSettingsController::updateSystemSettings', ['as' => 'trips.manager.update_settings', 'filter' => 'auth:manager,admin']);

    // Fleet Vehicles CRUD via FleetController (manager/admin role)
    $routes->post('vehicles/add', 'FleetController::addVehicle', ['as' => 'trips.vehicle.add', 'filter' => 'auth:manager,admin']);
    $routes->post('vehicles/edit', 'FleetController::editVehicle', ['as' => 'trips.vehicle.edit', 'filter' => 'auth:manager,admin']);
    $routes->post('vehicles/delete/(:num)', 'FleetController::deleteVehicle/$1', ['as' => 'trips.vehicle.delete', 'filter' => 'auth:manager,admin']);

    // Fleet Drivers CRUD via FleetController (manager/admin role)
    $routes->post('drivers/add', 'FleetController::addDriver', ['as' => 'trips.driver.add', 'filter' => 'auth:manager,admin']);
    $routes->post('drivers/edit', 'FleetController::editDriver', ['as' => 'trips.driver.edit', 'filter' => 'auth:manager,admin']);
    $routes->post('drivers/delete/(:num)', 'FleetController::deleteDriver/$1', ['as' => 'trips.driver.delete', 'filter' => 'auth:manager,admin']);

    // Reports (any authenticated user)
    $routes->get('reports', 'ReportController::index', ['as' => 'trips.reports', 'filter' => 'auth']);
    $routes->get('reports/csv', 'ReportController::exportCsv', ['as' => 'trips.reports.csv', 'filter' => 'auth']);
});
