<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\TripCoordinateModel;
use App\Modules\Trips\Models\FuelRateModel;
use App\Modules\Trips\Entities\TripCoordinate;
use App\Modules\Trips\Entities\FuelRate;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class TrackingController extends BaseController
{
    /**
     * Driver workspace panel listing assigned bookings
     */
    public function driverWorkspace(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'driver') {
            return redirect()->to(url_to('auth.login'));
        }

        $db = \Config\Database::connect();
        
        // Find driver id linked to user session
        $driverRow = $db->table('drivers')->where('user_id', session()->get('userId'))->get()->getRow();
        if ($driverRow === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Driver record not found.');
        }

        $bookingModel = new BookingModel();
        // Fetch driver active or pending trips
        $bookings = $bookingModel->select('bookings.*, vehicles.plate_number, vehicles.model')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->where('driver_id', $driverRow->id)
            ->whereIn('trip_status', ['pending', 'active'])
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll();

        return view('App\Modules\Trips\Views\driver', [
            'pageTitle'       => 'Driver Workspace | Kong Safaris',
            'metaDescription' => 'Start trips and update coordinates on safari drives.',
            'canonicalUrl'    => url_to('trips.driver'),
            'robotsTag'       => 'noindex, nofollow',
            'bookings'        => $bookings,
        ]);
    }

    /**
     * Start/complete a trip
     */
    public function updateTripStatus(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'driver') {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int)$this->request->getPost('booking_id');
        $status     = (string)$this->request->getPost('status');

        if (! in_array($status, ['active', 'completed'], true)) {
            return redirect()->back()->with('error', 'Invalid status update request.');
        }

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking record not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $booking->trip_status = $status;
            $bookingModel->update($booking->id, $booking);

            // If completed, release vehicle and driver
            if ($status === 'completed') {
                $driverModel = new DriverModel();
                /** @var \App\Modules\Trips\Entities\Driver|null $driver */
                $driver = $driverModel->find($booking->driver_id);
                if ($driver !== null) {
                    $driver->status = 'available';
                    $driverModel->update($driver->id, $driver);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed while updating trip status.');
            }

            $msg = $status === 'active' ? 'Trip has started. Tracking active.' : 'Trip has successfully completed!';
            return redirect()->back()->with('success', $msg);

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Failed to update trip status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during state transition.');
        }
    }

    /**
     * AJAX Coordinate POST update endpoint
     */
    public function updateLocation(): ResponseInterface
    {
        $rules = [
            'booking_id' => 'required|integer',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'     => 'validation_error',
                'message'    => 'Invalid tracking metrics.',
                'result'     => [],
                'errors'     => $this->validator->getErrors(),
                'csrf_token' => csrf_hash(),
            ]);
        }

        $booking_id = (int)$this->request->getPost('booking_id');
        $lat        = (float)$this->request->getPost('latitude');
        $lng        = (float)$this->request->getPost('longitude');

        $bookingModel = new BookingModel();
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $bookingModel->select('id, trip_status')->find($booking_id);

        if ($booking === null) {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Booking record not found.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        if ($booking->trip_status !== 'active') {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Tracking coordinate logging rejected: Trip is not active.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        $tripCoordinateModel = new TripCoordinateModel();
        
        $coordinate = new TripCoordinate([
            'booking_id' => $booking->id,
            'latitude'   => $lat,
            'longitude'  => $lng,
            'created_at' => Time::now()->toDateTimeString(),
        ]);

        $tripCoordinateModel->insert($coordinate);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Coordinates updated.',
            'result'     => [],
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * AJAX fetch coordinates history for manager dashboard tracking map modal
     */
    public function getCoordinates(int $booking_id): ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized access.',
            ]);
        }

        $tripCoordinateModel = new TripCoordinateModel();
        $coords = $tripCoordinateModel->select('latitude, longitude, created_at')
            ->where('booking_id', $booking_id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Coordinates loaded.',
            'result'     => $coords,
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * Manager Panel view listing all bookings paginated with Bootstrap 5
     */
    /**
     * Manager Panel view listing all bookings paginated with Bootstrap 5
     */
    public function managerDashboard(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $bookingModel = new BookingModel();
        
        // Paginate bookings
        $bookings = $bookingModel->select('bookings.*, vehicles.plate_number, vehicles.model, users.first_name, users.last_name')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->join('drivers', 'drivers.id = bookings.driver_id')
            ->join('users', 'users.id = drivers.user_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->paginate(10, 'default');

        $fuelRateModel = new FuelRateModel();
        /** @var \App\Modules\Trips\Entities\FuelRate|null $fuelRate */
        $fuelRate = $fuelRateModel->orderBy('created_at', 'DESC')->first();

        // Load Vehicles
        $vehicleModel = new VehicleModel();
        $vehicles = $vehicleModel->findAll();

        // Load Drivers
        $db = \Config\Database::connect();
        $drivers = $db->table('drivers')
            ->select('drivers.id, drivers.license_number, drivers.allowance_flat_rate, drivers.status, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = drivers.user_id')
            ->get()
            ->getResultArray();

        // Load Refund Requests
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
            'currentFuelRate' => $fuelRate !== null ? (float)$fuelRate->price_per_liter : 1.45,
            'googleApiKey'    => env('GoogleMaps.APIKey') ?? '',
            'vehicles'        => $vehicles,
            'drivers'         => $drivers,
            'refundRequests'  => $refundRequests,
        ]);
    }

    /**
     * Update global fuel rate
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

        $price = (float)$this->request->getPost('price_per_liter');

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
     * Process refund requests manually or via Paystack
     */
    public function processRefund(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int)$this->request->getPost('booking_id');
        $action     = (string)$this->request->getPost('action'); // 'refund_paystack' or 'refund_manual'

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
                $paystackService = new \App\Modules\Payments\Libraries\PaystackService();
                $refund = $paystackService->initiateRefund($booking->paystack_reference ?? '', (float)$booking->total_price);
                
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
            log_message('error', 'Refund Processing Failure: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process refund.');
        }
    }

    /**
     * Add vehicle
     */
    public function addVehicle(): ResponseInterface
    {
        $rules = [
            'plate_number'                => 'required|is_unique[vehicles.plate_number]',
            'model'                       => 'required|string',
            'fuel_efficiency'             => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km' => 'required|numeric',
            'maintenance_reserve_per_km'  => 'required|numeric',
            'status'                      => 'required|in_list[active,maintenance,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vehicleModel = new VehicleModel();
        $vehicle = new \App\Modules\Trips\Entities\Vehicle($this->request->getPost());
        $vehicleModel->insert($vehicle);

        return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle added successfully.');
    }

    /**
     * Edit vehicle
     */
    public function editVehicle(): ResponseInterface
    {
        $id = (int)$this->request->getPost('vehicle_id');
        $rules = [
            'plate_number'                => "required|is_unique[vehicles.plate_number,id,{$id}]",
            'model'                       => 'required|string',
            'fuel_efficiency'             => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km' => 'required|numeric',
            'maintenance_reserve_per_km'  => 'required|numeric',
            'status'                      => 'required|in_list[active,maintenance,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($id);
        if ($vehicle !== null) {
            $vehicle->fill($this->request->getPost());
            $vehicleModel->update($id, $vehicle);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Delete vehicle
     */
    public function deleteVehicle(int $id): ResponseInterface
    {
        $vehicleModel = new VehicleModel();
        try {
            $vehicleModel->delete($id);
            return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Cannot delete vehicle. It may be referenced in bookings.');
        }
    }

    /**
     * Add driver
     */
    public function addDriver(): ResponseInterface
    {
        $rules = [
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'email'               => 'required|valid_email|is_unique[users.email]',
            'password'            => 'required|min_length[6]',
            'license_number'      => 'required|is_unique[drivers.license_number]',
            'allowance_flat_rate' => 'required|numeric',
            'status'              => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user details
            $user = new \App\Modules\Auth\Entities\User([
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'role'       => 'driver',
            ]);
            $user->setPassword((string)$this->request->getPost('password'));
            
            $userModel = new \App\Modules\Auth\Models\UserModel();
            $userModel->insert($user);
            $userId = $db->insertID();

            // Create driver details
            $driver = new \App\Modules\Trips\Entities\Driver([
                'user_id'             => $userId,
                'license_number'      => $this->request->getPost('license_number'),
                'allowance_flat_rate' => $this->request->getPost('allowance_flat_rate'),
                'status'              => $this->request->getPost('status'),
            ]);
            
            $driverModel = new DriverModel();
            $driverModel->insert($driver);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to save driver transactional records.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Driver registered successfully.');

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Add Driver Failure: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while registering the driver.');
        }
    }

    /**
     * Edit driver
     */
    public function editDriver(): ResponseInterface
    {
        $id = (int)$this->request->getPost('driver_id');
        $rules = [
            'license_number'      => "required|is_unique[drivers.license_number,id,{$id}]",
            'allowance_flat_rate' => 'required|numeric',
            'status'              => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $driverModel = new DriverModel();
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find($id);
        if ($driver !== null) {
            $driver->fill($this->request->getPost());
            $driverModel->update($id, $driver);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', 'Driver details updated.');
    }

    /**
     * Delete driver
     */
    public function deleteDriver(int $id): ResponseInterface
    {
        $driverModel = new DriverModel();
        $db = \Config\Database::connect();
        
        $driver = $driverModel->find($id);
        if ($driver === null) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        $db->transStart();

        try {
            $userId = $driver->user_id;
            $driverModel->delete($driver->id);
            
            // Delete user details
            $userModel = new \App\Modules\Auth\Models\UserModel();
            $userModel->delete($userId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to delete driver record transaction.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Driver record deleted.');

        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Cannot delete driver. Active references exist.');
        }
    }

    /**
     * Reassign/Assign driver to a booking (Manager action)
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

        $booking_id = (int)$this->request->getPost('booking_id');
        $new_driver_id  = (int)$this->request->getPost('driver_id');

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
            $old_driver_id = (int)$booking->driver_id;
            
            // Update the booking record
            $booking->driver_id = $newDriver->id;
            $bookingModel->update($booking->id, $booking);

            // Update driver statuses if trip is active
            if ($booking->trip_status === 'active') {
                // Set new driver to 'on_trip'
                $newDriver->status = 'on_trip';
                $driverModel->update($newDriver->id, $newDriver);

                // Revert old driver to 'available'
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
            log_message('error', 'Reassign Driver Failure: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign driver.');
        }
    }
}
