<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\FuelRateModel;
use App\Modules\Trips\Entities\FuelRate;
use App\Modules\Trips\Libraries\TripQueryService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * ManagerDashboardController
 *
 * Orchestrates the manager operations panel including dashboard view,
 * fuel rate updates, and delegation to service classes for all
 * booking, payment, and fleet operations.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class ManagerDashboardController extends BaseController
{
    private TripQueryService $queryService;

    public function __construct()
    {
        $this->queryService = service('tripQueryService');
    }

    /**
     * Manager dashboard view listing all bookings with pagination.
     */
    public function index(): string|ResponseInterface
    {
        $dashboardData  = $this->queryService->getDashboardBookings(10);
        $fuelRates      = $this->queryService->getCurrentFuelRates();
        $vehicles       = $this->queryService->getActiveVehicles();
        $drivers        = $this->queryService->getDriversList();
        $refundRequests = $this->queryService->getRefundRequests();
        $baseFeeSetting = $this->queryService->getSystemSetting('base_booking_fee');

        return view('App\Modules\Trips\Views\manager', [
            'pageTitle'          => 'Manager Panel | Kong Safaris Operations',
            'metaDescription'    => 'Monitor fleet operations, pricing, bookings, and active trips.',
            'canonicalUrl'       => url_to('trips.manager'),
            'robotsTag'          => 'noindex, nofollow',
            'bookings'           => $dashboardData['bookings'],
            'pager'              => $dashboardData['pager'],
            'currentPetrolRate'  => $fuelRates['petrol'] ?? 1.45,
            'currentDieselRate'  => $fuelRates['diesel'] ?? 1.35,
            'googleApiKey'       => env('GoogleMaps.APIKey') ?? '',
            'vehicles'           => $vehicles,
            'drivers'            => $drivers,
            'refundRequests'     => $refundRequests,
            'base_booking_fee'   => $baseFeeSetting !== null ? (float) $baseFeeSetting->setting_value : 50.00,
        ]);
    }

    /**
     * Update the global fuel rate.
     */
    public function updateFuelRate(): ResponseInterface
    {
        $rules = [
            'fuel_type'       => 'required|in_list[petrol,diesel]',
            'price_per_liter' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $fuelType = (string) $this->request->getPost('fuel_type');
        $price    = (float) $this->request->getPost('price_per_liter');

        $fuelRateModel = new FuelRateModel();
        $rate = new FuelRate([
            'fuel_type'       => $fuelType,
            'price_per_liter' => $price,
            'updated_by'      => session()->get('userId'),
            'created_at'      => Time::now()->toDateTimeString(),
        ]);

        $fuelRateModel->insert($rate);

        return redirect()->to(url_to('trips.manager'))
            ->with('success', ucfirst($fuelType) . ' fuel rate updated successfully to $' . number_format($price, 2) . ' per liter.');
    }

    /**
     * Display manual booking creation form.
     */
    public function manualBookingView(): string|ResponseInterface
    {
        $customers = $this->queryService->getCustomersList();
        $vehicles  = $this->queryService->getVehiclesList();
        $drivers   = $this->queryService->getActiveDriversList();

        return view('App\Modules\Trips\Views\manual_booking', [
            'pageTitle'       => 'Manual Booking | Kong Safaris',
            'metaDescription' => 'Create a booking on behalf of a customer.',
            'canonicalUrl'    => url_to('trips.manager.manual_booking'),
            'robotsTag'       => 'noindex, nofollow',
            'googleApiKey'    => env('GoogleMaps.APIKey') ?? '',
            'customers'       => $customers,
            'vehicles'        => $vehicles,
            'drivers'         => $drivers,
        ]);
    }
}
