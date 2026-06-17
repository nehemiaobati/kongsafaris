<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\FuelRateModel;
use App\Modules\Trips\Entities\FuelRate;
use App\Modules\Payments\Libraries\PaystackService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * ManagerDashboardController
 *
 * Handles the manager operations panel including dashboard view,
 * fuel rate updates, refund processing, and driver assignment.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class ManagerDashboardController extends BaseController
{
    /**
     * Manager dashboard view listing all bookings with pagination.
     */
    public function index(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $bookingModel = new BookingModel();

        $bookings = $bookingModel->select('bookings.*, vehicles.plate_number, vehicles.model, users.first_name, users.last_name')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->join('drivers', 'drivers.id = bookings.driver_id')
            ->join('users', 'users.id = drivers.user_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->paginate(10, 'default');

        $fuelRateModel = new FuelRateModel();
        /** @var \App\Modules\Trips\Entities\FuelRate|null $fuelRate */
        $fuelRate = $fuelRateModel->orderBy('created_at', 'DESC')->first();

        $vehicleModel = new VehicleModel();
        $vehicles = $vehicleModel->findAll();

        $db = \Config\Database::connect();
        $drivers = $db->table('drivers')
            ->select('drivers.id, drivers.license_number, drivers.allowance_flat_rate, drivers.status, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = drivers.user_id')
            ->get()
            ->getResultArray();

        $refundRequests = $bookingModel->select('bookings.*, users.first_name, users.last_name')
            ->join('users', 'users.id = bookings.customer_id')
            ->where('payment_status', 'refund_requested')
            ->findAll();

        return view('App\Modules\Trips\Views\manager', [
            'pageTitle'       => 'Manager Panel | Kong Safaris Operations',
            'metaDescription' => 'Monitor fleet operations, pricing, bookings, and active trips.',
            'canonicalUrl'    => url_to('trips.manager'),
            'robotsTag'       => 'noindex, nofollow',
            'bookings'        => $bookings,
            'pager'           => $bookingModel->pager,
            'currentFuelRate' => $fuelRate !== null ? (float) $fuelRate->price_per_liter : 1.45,
            'googleApiKey'    => env('GoogleMaps.APIKey') ?? '',
            'vehicles'        => $vehicles,
            'drivers'         => $drivers,
            'refundRequests'  => $refundRequests,
        ]);
    }

    /**
     * Update the global fuel rate.
     */
    public function updateFuelRate(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $rules = [
            'price_per_liter' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $price = (float) $this->request->getPost('price_per_liter');

        $fuelRateModel = new FuelRateModel();
        $rate = new FuelRate([
            'price_per_liter' => $price,
            'updated_by'      => session()->get('userId'),
            'created_at'      => Time::now()->toDateTimeString(),
        ]);

        $fuelRateModel->insert($rate);

        return redirect()->to(url_to('trips.manager'))
            ->with('success', 'Global fuel rate updated successfully to $' . number_format($price, 2) . ' per liter.');
    }

    /**
     * Process refund requests via Paystack or manual clearance.
     */
    public function processRefund(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int) $this->request->getPost('booking_id');
        $action     = (string) $this->request->getPost('action');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null || $booking->payment_status !== 'refund_requested') {
            return redirect()->back()->with('error', 'Invalid booking selected for refund.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($action === 'refund_paystack') {
                $paystackService = service('paystackService');
                $refund = $paystackService->initiateRefund($booking->paystack_reference ?? '', (float) $booking->total_price);

                if (! $refund['status']) {
                    return redirect()->back()->with('error', 'Paystack Refund Error: ' . ($refund['message'] ?? 'Unknown error.'));
                }
            }

            $booking->payment_status = 'refunded';
            $bookingModel->update($booking->id, $booking);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to save refund status.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Refund cleared successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Refund Processing Failure', ['booking_id' => $booking_id, 'exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to process refund.');
        }
    }

    /**
     * Reassign or assign a driver to a booking.
     */
    public function assignDriver(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $rules = [
            'booking_id' => 'required|integer',
            'driver_id'  => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $booking_id   = (int) $this->request->getPost('booking_id');
        $new_driver_id = (int) $this->request->getPost('driver_id');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking record not found.');
        }

        $driverModel = new DriverModel();
        /** @var \App\Modules\Trips\Entities\Driver|null $newDriver */
        $newDriver = $driverModel->find($new_driver_id);

        if ($newDriver === null) {
            return redirect()->back()->with('error', 'Selected driver not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $old_driver_id = (int) $booking->driver_id;

            $booking->driver_id = $newDriver->id;
            $bookingModel->update($booking->id, $booking);

            if ($booking->trip_status === 'active') {
                $newDriver->status = 'on_trip';
                $driverModel->update($newDriver->id, $newDriver);

                /** @var \App\Modules\Trips\Entities\Driver|null $oldDriver */
                $oldDriver = $driverModel->find($old_driver_id);
                if ($oldDriver !== null) {
                    $oldDriver->status = 'available';
                    $driverModel->update($oldDriver->id, $oldDriver);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to reassign driver transactionally.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Driver reassigned successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Reassign Driver Failure', ['exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to assign driver.');
        }
    }

    /**
     * Allow manager to cancel a pending trip (same safe logic as customer).
     */
    public function cancelBooking(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int) $this->request->getPost('booking_id');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        if ($booking->trip_status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending trips can be cancelled.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $new_trip_status = 'cancelled';
            $new_payment_status = in_array($booking->payment_status, ['paid', 'manual_verified'], true) ? 'refund_requested' : $booking->payment_status;

            $bookingModel->update($booking->id, [
                'trip_status' => $new_trip_status,
                'payment_status' => $new_payment_status,
            ]);

            // Release driver if assigned
            $driverModel = new DriverModel();
            if (!empty($booking->driver_id)) {
                $driverModel->update((int)$booking->driver_id, ['status' => 'available']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to cancel booking.');
            }

            return redirect()->to(url_to('trips.manager'))
                ->with('success', 'Booking #' . $booking_id . ' has been cancelled.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Manager Cancel Booking Failure', [
                'booking_id' => $booking_id,
                'trip_status' => $booking->trip_status,
                'payment_status' => $booking->payment_status,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }

    /**
     * Allow manager to initiate payment collection for pending bookings (or retry failed).
     */
    public function initiatePayment(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $rules = [
            'booking_id' => 'required|integer',
            'provider'   => 'required|in_list[card,mpesa,airtel]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $booking_id = (int) $this->request->getPost('booking_id');
        $provider   = (string) $this->request->getPost('provider');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        if (! in_array($booking->payment_status, ['pending', 'failed'], true)) {
            return redirect()->back()->with('error', 'Payment can only be collected for pending or failed payments.');
        }

        $vehicleModel = new VehicleModel();
        $driverModel  = new DriverModel();

        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $vehicleModel->find((int)$booking->vehicle_id);
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find((int)$booking->driver_id);

        if ($vehicle === null || $driver === null) {
            return redirect()->back()->with('error', 'Vehicle or driver configuration missing for this booking.');
        }

        $customer_id = (int)$booking->customer_id;
        $userModel   = new \App\Modules\Auth\Models\UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $customer */
        $customer = $userModel->find($customer_id);
        $email    = $customer !== null ? (string)$customer->email : 'customer@kongsafaris.com';

        $paystackService = new \App\Modules\Payments\Libraries\PaystackService();

        $metadata = [
            'customer_id'         => $customer_id,
            'booking_id'          => $booking_id,
            'vehicle_id'          => $booking->vehicle_id,
            'driver_id'           => $booking->driver_id,
            'pickup_address'      => $booking->pickup_address,
            'dropoff_address'     => $booking->dropoff_address,
            'pickup_latitude'     => $booking->pickup_latitude,
            'pickup_longitude'    => $booking->pickup_longitude,
            'dropoff_latitude'    => $booking->dropoff_latitude,
            'dropoff_longitude'   => $booking->dropoff_longitude,
            'distance_km'         => $booking->distance_km,
            'base_booking_fee'    => $booking->base_booking_fee,
            'per_km_fuel_cost'    => $booking->per_km_fuel_cost,
            'maintenance_reserve' => $booking->maintenance_reserve,
            'driver_allowance'    => $booking->driver_allowance,
            'total_price'         => $booking->total_price,
        ];

        if ($provider === 'card') {
            $init = $paystackService->initializeTransaction((float)$booking->total_price, $email, $metadata);
        } else {
            $init = $paystackService->initializeMobileMoneyCharge((float)$booking->total_price, $email, $provider, $metadata);
        }

        if (! $init['status']) {
            return redirect()->back()->with('error', 'Payment initialization failed: ' . ($init['message'] ?? 'Unknown error.'));
        }

        // All providers now return a hosted checkout URL — redirect there for the complete payment flow
        if (! empty($init['authorization_url'])) {
            return redirect()->to($init['authorization_url']);
        }

        return redirect()->to(url_to('trips.manager'))
            ->with('error', 'Payment initialized but no redirect URL was returned. Please try again or contact support.');
    }

    // --- Admin Override Tools ---

    /**
     * Display manual booking creation form.
     */
    public function manualBookingView(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $db = \Config\Database::connect();
        $customers = $db->table('users')
            ->select('id, first_name, last_name, email')
            ->where('role', 'customer')
            ->orderBy('first_name', 'ASC')
            ->get()
            ->getResultArray();

        $vehicleModel = new VehicleModel();
        $vehicles = $vehicleModel->where('status', 'active')->findAll();

        $drivers = $db->table('drivers')
            ->select('drivers.id, drivers.status, users.first_name, users.last_name')
            ->join('users', 'users.id = drivers.user_id')
            ->get()
            ->getResultArray();

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

    /**
     * Process manual booking creation by manager.
     */
    public function manualBookingCreate(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $rules = [
            'customer_id'     => 'required|integer',
            'vehicle_id'      => 'required|integer',
            'driver_id'       => 'required|integer',
            'pickup_address'  => 'required|string',
            'dropoff_address' => 'required|string',
            'pickup_latitude'   => 'required|numeric',
            'pickup_longitude'  => 'required|numeric',
            'dropoff_latitude'  => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'distance_km'       => 'required|numeric|greater_than[0]',
            'total_price'       => 'required|numeric|greater_than[0]',
            'payment_status'    => 'required|in_list[pending,paid,manual_verified]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $booking = new \App\Modules\Trips\Entities\Booking([
                'customer_id'         => (int) $this->request->getPost('customer_id'),
                'vehicle_id'          => (int) $this->request->getPost('vehicle_id'),
                'driver_id'           => (int) $this->request->getPost('driver_id'),
                'pickup_address'      => (string) $this->request->getPost('pickup_address'),
                'dropoff_address'     => (string) $this->request->getPost('dropoff_address'),
                'pickup_latitude'     => (float) $this->request->getPost('pickup_latitude'),
                'pickup_longitude'    => (float) $this->request->getPost('pickup_longitude'),
                'dropoff_latitude'    => (float) $this->request->getPost('dropoff_latitude'),
                'dropoff_longitude'   => (float) $this->request->getPost('dropoff_longitude'),
                'distance_km'         => (float) $this->request->getPost('distance_km'),
                'total_price'         => (float) $this->request->getPost('total_price'),
                'base_booking_fee'    => 0.00,
                'per_km_fuel_cost'    => 0.00,
                'maintenance_reserve' => 0.00,
                'driver_allowance'    => 0.00,
                'payment_status'      => (string) $this->request->getPost('payment_status'),
                'trip_status'         => 'pending',
                'paystack_reference'  => (string) ($this->request->getPost('paystack_reference') ?? 'MANUAL-' . bin2hex(random_bytes(4))),
            ]);

            $bookingModel = new BookingModel();
            $bookingModel->insert($booking);

            // Set driver to on_trip if paid
            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $driverModel = new DriverModel();
                /** @var \App\Modules\Trips\Entities\Driver|null $driver */
                $driver = $driverModel->find($booking->driver_id);
                if ($driver !== null) {
                    $driverModel->update($driver->id, ['status' => 'on_trip']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to create manual booking.');
            }

            return redirect()->to(url_to('trips.manager'))
                ->with('success', 'Manual booking #' . $db->insertID() . ' created successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Manual Booking Failed', ['exception' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create manual booking.');
        }
    }

    /**
     * Force cancel any booking regardless of trip status.
     */
    public function forceCancelBooking(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int) $this->request->getPost('booking_id');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $booking->trip_status = 'cancelled';

            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $booking->payment_status = 'refund_requested';
            }

            $bookingModel->update($booking->id, $booking);

            // Release driver
            $driverModel = new DriverModel();
            /** @var \App\Modules\Trips\Entities\Driver|null $driver */
            $driver = $driverModel->find($booking->driver_id);
            if ($driver !== null) {
                $driver->status = 'available';
                $driverModel->update($driver->id, $driver);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to force cancel booking.');
            }

            return redirect()->to(url_to('trips.manager'))
                ->with('success', 'Booking #' . $booking_id . ' has been force cancelled.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Force Cancel Failed', ['booking_id' => $booking_id, 'exception' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to cancel booking.');
        }
    }

    /**
     * Override payment status on a booking (mark as paid manually).
     */
    public function overridePaymentStatus(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $rules = [
            'booking_id'       => 'required|integer',
            'payment_status'   => 'required|in_list[pending,paid,failed,manual_verified,refunded]',
            'paystack_reference' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $booking_id = (int) $this->request->getPost('booking_id');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $booking->payment_status = (string) $this->request->getPost('payment_status');

        $reference = (string) $this->request->getPost('paystack_reference');
        if (! empty($reference)) {
            $booking->paystack_reference = $reference;
        }

        $bookingModel->update($booking->id, $booking);

        return redirect()->to(url_to('trips.manager'))
            ->with('success', 'Booking #' . $booking_id . ' payment status updated to ' . $booking->payment_status . '.');
    }
}
