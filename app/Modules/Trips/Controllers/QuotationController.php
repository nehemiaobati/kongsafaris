<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Entities\Booking;
use CodeIgniter\HTTP\ResponseInterface;

class QuotationController extends BaseController
{
    /**
     * Display customer booking screen
     */
    public function index(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        $vehicleModel = new VehicleModel();
        $driverModel = new DriverModel();

        // Get active vehicles and drivers
        $vehicles = $vehicleModel->where('status', 'active')->findAll();

        // Find drivers and join with user details to get their names
        $db = \Config\Database::connect();
        $drivers = $db->table('drivers')
            ->select('drivers.id, drivers.allowance_flat_rate, users.first_name, users.last_name')
            ->join('users', 'users.id = drivers.user_id')
            ->where('drivers.status', 'available')
            ->get()
            ->getResultArray();

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
     * AJAX endpoint to calculate dynamic pricing details
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

        $vehicle_id = (int)$this->request->getPost('vehicle_id');
        $driver_id  = (int)$this->request->getPost('driver_id');

        $p_lat = (float)$this->request->getPost('pickup_latitude');
        $p_lng = (float)$this->request->getPost('pickup_longitude');
        $d_lat = (float)$this->request->getPost('dropoff_latitude');
        $d_lng = (float)$this->request->getPost('dropoff_longitude');

        $vehicleModel = new VehicleModel();
        $driverModel = new DriverModel();

        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $vehicleModel->find($vehicle_id);

        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find($driver_id);

        if ($vehicle === null || $driver === null) {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Vehicle or driver configurations not found.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        // Fetch distance via GeocodingService (Google API with Haversine fallback)
        $distance_km = service('geocodingService')->getDistance($p_lat, $p_lng, $d_lat, $d_lng);

        // Calculate Pricing using Service Library
        $pricingService = service('pricingService');
        $pricing = $pricingService->calculateQuote($vehicle, $driver, $distance_km);
        $pricing['distance_km'] = round($distance_km, 2);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Quote calculated successfully.',
            'result'     => $pricing,
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * Render the customer dashboard listing paid and refundable bookings
     */
    public function customerDashboard(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return redirect()->to(url_to('auth.login'));
        }

        $bookingModel = new BookingModel();

        // Load customer successful/paid bookings, omitting unpaid attempts
        $bookings = $bookingModel->select('bookings.*, vehicles.model, vehicles.plate_number, users.first_name, users.last_name')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->join('drivers', 'drivers.id = bookings.driver_id')
            ->join('users', 'users.id = drivers.user_id')
            ->where('customer_id', session()->get('userId'))
            ->whereIn('payment_status', ['paid', 'manual_verified', 'refund_requested', 'refunded'])
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll();

        return view('App\Modules\Trips\Views\customer_dashboard', [
            'pageTitle'       => 'My Bookings | Kong Safaris',
            'metaDescription' => 'Review your paid bookings, cancel trips, and request refunds.',
            'canonicalUrl'    => url_to('trips.customer.dashboard'),
            'robotsTag'       => 'noindex, nofollow',
            'bookings'        => $bookings,
        ]);
    }

    /**
     * Cancel an uninitiated booking
     */
    public function cancelBooking(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int)$this->request->getPost('booking_id');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null || (int)$booking->customer_id !== (int)session()->get('userId')) {
            return redirect()->back()->with('error', 'Booking not found or access denied.');
        }

        if ($booking->trip_status !== 'pending') {
            return redirect()->back()->with('error', 'Only uninitiated (pending) trips can be cancelled.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $booking->trip_status = 'cancelled';

            // If already paid, request a refund
            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $booking->payment_status = 'refund_requested';
            }

            $bookingModel->update($booking->id, $booking);

            // Set driver back to available
            $driverModel = new DriverModel();
            /** @var \App\Modules\Trips\Entities\Driver|null $driver */
            $driver = $driverModel->find($booking->driver_id);
            if ($driver !== null) {
                $driver->status = 'available';
                $driverModel->update($driver->id, $driver);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to cancel booking in database.');
            }

            $msg = 'Your safari transfer has been cancelled.';
            if ($booking->payment_status === 'refund_requested') {
                $msg .= ' A refund request has been logged for management processing.';
            }

            return redirect()->to(url_to('trips.customer.dashboard'))->with('success', $msg);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Customer Booking Cancellation Failure: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process cancellation.');
        }
    }
}
