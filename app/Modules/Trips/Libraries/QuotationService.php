<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\VehicleModel;
use CodeIgniter\Database\ConnectionInterface;

/**
 * QuotationService
 *
 * Handles quote calculation, customer dashboard data, reverse geocoding
 * address formatting, and customer-initiated booking cancellation.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class QuotationService
{
    private ConnectionInterface $db;
    private BookingModel $bookingModel;
    private VehicleModel $vehicleModel;
    private DriverModel $driverModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->bookingModel = new BookingModel();
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
    }

    /**
     * Get active vehicles for quotation selection.
     *
     * @return array
     */
    public function getActiveVehicles(): array
    {
        return $this->vehicleModel->where('status', 'active')->findAll();
    }

    /**
     * Get available drivers for quotation selection (joined with user details).
     *
     * @return array
     */
    public function getAvailableDrivers(): array
    {
        return $this->db->table('drivers')
            ->select('drivers.id, drivers.allowance_flat_rate, users.first_name, users.last_name')
            ->join('users', 'users.id = drivers.user_id')
            ->where('drivers.status', 'available')
            ->get()
            ->getResultArray();
    }

    /**
     * Get customer bookings with paid statuses.
     *
     * @param int $customerId
     *
     * @return array
     */
    public function getCustomerBookings(int $customerId): array
    {
        return $this->bookingModel
            ->select('bookings.*, vehicles.model, vehicles.plate_number, users.first_name, users.last_name')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->join('drivers', 'drivers.id = bookings.driver_id')
            ->join('users', 'users.id = drivers.user_id')
            ->where('customer_id', $customerId)
            ->whereIn('payment_status', ['paid', 'manual_verified', 'refund_requested', 'refunded'])
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Cancel a customer's own pending booking.
     *
     * @param int $bookingId
     * @param int $customerId Session user ID for ownership verification
     *
     * @return array{status: bool, message: string}
     */
    public function cancelCustomerBooking(int $bookingId, int $customerId): array
    {
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null || (int) $booking->customer_id !== $customerId) {
            return ['status' => false, 'message' => 'Booking not found or access denied.'];
        }

        if ($booking->trip_status !== 'pending') {
            return ['status' => false, 'message' => 'Only uninitiated (pending) trips can be cancelled.'];
        }

        $this->db->transStart();

        try {
            $booking->trip_status = 'cancelled';

            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $booking->payment_status = 'refund_requested';
            }

            $this->bookingModel->update($booking->id, $booking);

            // Set driver back to available
            $driver = $this->driverModel->find($booking->driver_id);
            if ($driver !== null) {
                $driver->status = 'available';
                $this->driverModel->update($driver->id, $driver);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to cancel booking in database.');
            }

            $msg = 'Your safari transfer has been cancelled.';
            if ($booking->payment_status === 'refund_requested') {
                $msg .= ' A refund request has been logged for management processing.';
            }

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Customer Booking Cancellation Failed', [
                'booking_id' => $bookingId,
                'exception'  => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Failed to process cancellation.'];
        }
    }
}