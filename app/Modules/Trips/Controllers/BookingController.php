<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Libraries\QuotationService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * QuotationController
 *
 * Handles customer-facing booking screen, quote calculation,
 * and customer-initiated cancellations. Delegates business logic
 * to QuotationService and external services.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class QuotationController extends BaseController
{
    private QuotationService $quotationService;

    public function __construct()
    {
        $this->quotationService = service('quotationService');
    }

    /**
     * Display customer booking screen.
     */
    public function index(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        $vehicles = $this->quotationService->getActiveVehicles();
        $drivers  = $this->quotationService->getAvailableDrivers();

        return view('App\Modules\Trips\Views\quote', [
            'pageTitle'       => 'Book Your Safari | Kong Safaris',
            'metaDescription' => 'Request a dynamic quote and book a safari vehicle with Kong Safaris.',
            'canonicalUrl'    => url_to('trips.quote'),
            'robotsTag'       => 'noindex, nofollow',
            'vehicles'        => $vehicles,
            'drivers'         => $drivers,
            'googleApiKey'    => env('GoogleMaps.APIKey') ?? '',
        ]);
    }

    /**
     * AJAX endpoint to calculate dynamic pricing details.
     */
    public function calculate(): ResponseInterface
    {
        $rules = [
            'vehicle_id'        => 'required|integer',
            'driver_id'         => 'required|integer',
            'pickup_latitude'   => 'required|numeric',
            'pickup_longitude'  => 'required|numeric',
            'dropoff_latitude'  => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'     => 'validation_error',
                'message'    => 'Invalid coordinates, vehicle or driver parameters.',
                'result'     => [],
                'errors'     => $this->validator->getErrors(),
                'csrf_token' => csrf_hash(),
            ]);
        }

        $vehicleId = (int) $this->request->getPost('vehicle_id');
        $driverId  = (int) $this->request->getPost('driver_id');

        $pLat = (float) $this->request->getPost('pickup_latitude');
        $pLng = (float) $this->request->getPost('pickup_longitude');
        $dLat = (float) $this->request->getPost('dropoff_latitude');
        $dLng = (float) $this->request->getPost('dropoff_longitude');

        $vehicleModel = new VehicleModel();
        $driverModel  = new DriverModel();

        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $vehicleModel->find($vehicleId);
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find($driverId);

        if ($vehicle === null || $driver === null) {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Vehicle or driver configurations not found.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        $distanceKm = service('geocodingService')->getDistance($pLat, $pLng, $dLat, $dLng);
        $pricing    = service('pricingService')->calculateQuote($vehicle, $driver, $distanceKm);
        $pricing['distance_km'] = round($distanceKm, 2);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Quote calculated successfully.',
            'result'     => $pricing,
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * Render the customer dashboard listing paid and refundable bookings.
     */
    public function customerDashboard(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return redirect()->to(url_to('auth.login'));
        }

        $bookings = $this->quotationService->getCustomerBookings((int) session()->get('userId'));

        return view('App\Modules\Trips\Views\customer_dashboard', [
            'pageTitle'       => 'My Bookings | Kong Safaris',
            'metaDescription' => 'Review your paid bookings, cancel trips, and request refunds.',
            'canonicalUrl'    => url_to('trips.customer.dashboard'),
            'robotsTag'       => 'noindex, nofollow',
            'bookings'        => $bookings,
        ]);
    }

    /**
     * Reverse geocode coordinates to readable address (AJAX).
     */
    public function reverseGeocode(): ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Unauthorized.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        $lat = (float) $this->request->getPost('latitude');
        $lng = (float) $this->request->getPost('longitude');

        $address = service('geocodingService')->reverseGeocode($lat, $lng);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Reverse geocoded.',
            'result'     => ['address' => $address],
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * Cancel a customer's own pending booking.
     */
    public function cancelBooking(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return redirect()->to(url_to('auth.login'));
        }

        $bookingId  = (int) $this->request->getPost('booking_id');
        $customerId = (int) session()->get('userId');

        $result = $this->quotationService->cancelCustomerBooking($bookingId, $customerId);

        if (! $result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(url_to('trips.customer.dashboard'))->with('success', $result['message']);
    }
}
