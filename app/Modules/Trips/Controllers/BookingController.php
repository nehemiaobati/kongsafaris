<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Entities\Booking;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * BookingController
 *
 * Handles booking lifecycle operations: creation, updates, cancellation.
 * Extracted from ManagerDashboardController to enforce single responsibility.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class BookingController extends BaseController
{
    private BookingModel $bookingModel;
    private DriverModel $driverModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->driverModel = new DriverModel();
    }

    /**
     * Process manual booking creation by manager.
     */
    public function manualBookingCreate(): ResponseInterface
    {
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
            $booking = new Booking([
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

            $this->bookingModel->insert($booking);

            // Set driver to on_trip if paid
            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $driver = $this->driverModel->find($booking->driver_id);
                if ($driver !== null) {
                    $this->driverModel->update($driver->id, ['status' => 'on_trip']);
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
     * Allow manager to cancel a pending trip.
     */
    public function cancelBooking(): ResponseInterface
    {
        $booking_id = (int) $this->request->getPost('booking_id');

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($booking_id);

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

            $this->bookingModel->update($booking->id, [
                'trip_status' => $new_trip_status,
                'payment_status' => $new_payment_status,
            ]);

            // Release driver if assigned
            if (!empty($booking->driver_id)) {
                $this->driverModel->update((int)$booking->driver_id, ['status' => 'available']);
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
     * Force cancel any booking regardless of trip status.
     */
    public function forceCancelBooking(): ResponseInterface
    {
        $booking_id = (int) $this->request->getPost('booking_id');

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($booking_id);

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

            $this->bookingModel->update($booking->id, $booking);

            // Release driver
            $driver = $this->driverModel->find($booking->driver_id);
            if ($driver !== null) {
                $driver->status = 'available';
                $this->driverModel->update($driver->id, $driver);
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
     * Update booking details from the Edit Booking modal.
     */
    public function updateBooking(): ResponseInterface
    {
        $rules = [
            'booking_id'       => 'required|integer',
            'pickup_address'   => 'required|string',
            'dropoff_address'  => 'required|string',
            'vehicle_id'       => 'required|integer',
            'driver_id'        => 'required|integer',
            'distance_km'      => 'required|numeric|greater_than[0]',
            'total_price'      => 'required|numeric|greater_than[0]',
            'payment_status'   => 'required|in_list[pending,paid,failed,manual_verified,refund_requested,refunded]',
            'trip_status'      => 'required|in_list[pending,active,completed,cancelled]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $booking_id = (int) $this->request->getPost('booking_id');

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $old_driver_id  = (int) $booking->driver_id;
            $old_trip_status = $booking->trip_status;
            $new_trip_status = (string) $this->request->getPost('trip_status');
            $new_driver_id  = (int) $this->request->getPost('driver_id');

            $updateData = [
                'pickup_address'  => (string) $this->request->getPost('pickup_address'),
                'dropoff_address' => (string) $this->request->getPost('dropoff_address'),
                'vehicle_id'      => (int) $this->request->getPost('vehicle_id'),
                'driver_id'       => $new_driver_id,
                'distance_km'     => (float) $this->request->getPost('distance_km'),
                'total_price'     => (float) $this->request->getPost('total_price'),
                'payment_status'  => (string) $this->request->getPost('payment_status'),
                'trip_status'     => $new_trip_status,
            ];

            $this->bookingModel->update($booking_id, $updateData);

            // If trip is being cancelled or completed, release the driver
            if (in_array($new_trip_status, ['cancelled', 'completed'], true) && $old_trip_status === 'active') {
                $driver = $this->driverModel->find($old_driver_id);
                if ($driver !== null) {
                    $this->driverModel->update($driver->id, ['status' => 'available']);
                }
            }

            // If trip is being activated, mark new driver as on_trip
            if ($new_trip_status === 'active' && $old_trip_status !== 'active') {
                $newDriver = $this->driverModel->find($new_driver_id);
                if ($newDriver !== null) {
                    $this->driverModel->update($newDriver->id, ['status' => 'on_trip']);
                }
            }

            // If driver changed on an active trip, swap statuses
            if ($new_trip_status === 'active' && $old_driver_id !== $new_driver_id) {
                $oldDriver = $this->driverModel->find($old_driver_id);
                if ($oldDriver !== null) {
                    $this->driverModel->update($oldDriver->id, ['status' => 'available']);
                }
                $newDriver = $this->driverModel->find($new_driver_id);
                if ($newDriver !== null) {
                    $this->driverModel->update($newDriver->id, ['status' => 'on_trip']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to update booking.');
            }

            return redirect()->to(url_to('trips.manager'))
                ->with('success', 'Booking #' . $booking_id . ' updated successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Update Booking Failure', [
                'booking_id' => $booking_id,
                'exception'  => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to update booking: ' . $e->getMessage());
        }
    }
}