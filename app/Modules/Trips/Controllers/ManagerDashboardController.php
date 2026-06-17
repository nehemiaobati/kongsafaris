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
}