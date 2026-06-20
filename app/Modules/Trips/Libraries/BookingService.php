<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Entities\Booking;
use App\Modules\Auth\Models\UserModel;
use App\Modules\Notifications\Libraries\EmailService;
use CodeIgniter\Database\ConnectionInterface;

/**
 * BookingService
 *
 * Encapsulates all booking lifecycle operations: create, cancel,
 * force-cancel, update, and manual booking creation.
 * Controllers MUST delegate to this service for all booking logic.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class BookingService
{
    private ConnectionInterface $db;
    private BookingModel $bookingModel;
    private DriverModel $driverModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->bookingModel = new BookingModel();
        $this->driverModel = new DriverModel();
    }

    /**
     * Create a booking from Paystack metadata after successful payment.
     *
     * @param string $reference Paystack transaction reference
     * @param array  $metadata  Metadata from Paystack callback/webhook
     *
     * @return int Booking ID (0 on failure)
     */
    public function createFromPayment(string $reference, array $metadata): int
    {
        if (empty($metadata)) {
            return 0;
        }

        // Idempotency: Check if this reference was already processed
        $existing = $this->bookingModel->where('paystack_reference', $reference)->first();
        if ($existing !== null) {
            if ($existing->payment_status !== 'paid') {
                $this->bookingModel->update($existing->id, ['payment_status' => 'paid']);
            }
            return (int) $existing->id;
        }

        // Upsert by booking_id when available (manager-initiated payments)
        $bookingIdFromMeta = isset($metadata['booking_id']) ? (int) $metadata['booking_id'] : 0;
        if ($bookingIdFromMeta > 0) {
            $booking = $this->bookingModel->find($bookingIdFromMeta);
            if ($booking !== null) {
                $this->bookingModel->update($booking->id, [
                    'paystack_reference' => $reference,
                    'payment_status'     => 'paid',
                ]);
                return (int) $booking->id;
            }
        }

        $this->db->transStart();

        try {
            $booking = new Booking([
                'customer_id'         => isset($metadata['customer_id']) ? (int) $metadata['customer_id'] : null,
                'vehicle_id'          => (int) $metadata['vehicle_id'],
                'driver_id'           => (int) $metadata['driver_id'],
                'pickup_address'      => (string) $metadata['pickup_address'],
                'dropoff_address'     => (string) $metadata['dropoff_address'],
                'pickup_latitude'     => (float) $metadata['pickup_latitude'],
                'pickup_longitude'    => (float) $metadata['pickup_longitude'],
                'dropoff_latitude'    => (float) $metadata['dropoff_latitude'],
                'dropoff_longitude'   => (float) $metadata['dropoff_longitude'],
                'distance_km'         => (float) $metadata['distance_km'],
                'base_booking_fee'    => (float) $metadata['base_booking_fee'],
                'per_km_fuel_cost'    => (float) $metadata['per_km_fuel_cost'],
                'maintenance_reserve' => (float) $metadata['maintenance_reserve'],
                'driver_allowance'    => (float) $metadata['driver_allowance'],
                'total_price'         => (float) $metadata['total_price'],
                'payment_status'      => 'paid',
                'trip_status'         => 'pending',
                'paystack_reference'  => $reference,
            ]);

            $this->bookingModel->insert($booking);
            $bookingId = (int) $this->db->insertID();

            // Set driver status to 'on_trip'
            $driver = $this->driverModel->find($booking->driver_id);
            if ($driver !== null) {
                $this->driverModel->update($driver->id, ['status' => 'on_trip']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed on post-payment booking creation.');
            }

            // Trigger email notification
            $this->_sendPaymentConfirmation($booking, $bookingId);

            return $bookingId;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('critical', 'Payment Booking Creation Exception', [
                'reference' => $reference,
                'exception' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Cancel a pending booking and release driver.
     *
     * @param int  $bookingId
     * @param bool $force      If true, cancels regardless of trip status
     *
     * @return array{status: bool, message: string}
     */
    public function cancelBooking(int $bookingId, bool $force = false): array
    {
        /** @var Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking not found.'];
        }

        if (! $force && $booking->trip_status !== 'pending') {
            return ['status' => false, 'message' => 'Only pending trips can be cancelled.'];
        }

        $this->db->transStart();

        try {
            $newPaymentStatus = in_array($booking->payment_status, ['paid', 'manual_verified'], true)
                ? 'refund_requested'
                : $booking->payment_status;

            $this->bookingModel->update($booking->id, [
                'trip_status'    => 'cancelled',
                'payment_status' => $newPaymentStatus,
            ]);

            // Release driver if assigned
            if (! empty($booking->driver_id)) {
                $this->driverModel->update((int) $booking->driver_id, ['status' => 'available']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to cancel booking in database.');
            }

            $msg = 'Booking #' . $bookingId . ' has been cancelled.';
            if ($newPaymentStatus === 'refund_requested') {
                $msg .= ' A refund request has been logged.';
            }

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Cancel Booking Failure', [
                'booking_id' => $bookingId,
                'exception'  => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Failed to cancel booking.'];
        }
    }

    /**
     * Update booking details including driver/trip status transitions.
     *
     * @param int   $bookingId
     * @param array $data Updated fields
     *
     * @return array{status: bool, message: string}
     */
    public function updateBooking(int $bookingId, array $data): array
    {
        /** @var Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking not found.'];
        }

        $oldDriverId    = (int) $booking->driver_id;
        $oldTripStatus  = $booking->trip_status;
        $newTripStatus  = (string) ($data['trip_status'] ?? $booking->trip_status);
        $newDriverId    = (int) ($data['driver_id'] ?? $booking->driver_id);

        $this->db->transStart();

        try {
            $updateData = [
                'pickup_address'   => $data['pickup_address'] ?? $booking->pickup_address,
                'dropoff_address'  => $data['dropoff_address'] ?? $booking->dropoff_address,
                'vehicle_id'       => (int) ($data['vehicle_id'] ?? $booking->vehicle_id),
                'driver_id'        => $newDriverId,
                'distance_km'      => (float) ($data['distance_km'] ?? $booking->distance_km),
                'total_price'      => (float) ($data['total_price'] ?? $booking->total_price),
                'payment_status'   => $data['payment_status'] ?? $booking->payment_status,
                'trip_status'      => $newTripStatus,
            ];

            $this->bookingModel->update($bookingId, $updateData);

            // Handle driver status transitions
            $this->_handleDriverStatusTransition(
                $oldTripStatus,
                $newTripStatus,
                $oldDriverId,
                $newDriverId
            );

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to update booking.');
            }

            return ['status' => true, 'message' => 'Booking #' . $bookingId . ' updated successfully.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Update Booking Failure', [
                'booking_id' => $bookingId,
                'exception'  => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Failed to update booking.'];
        }
    }

    /**
     * Create a manual booking (manager-initiated).
     *
     * @param array $data Booking fields from form submission
     *
     * @return array{status: bool, booking_id: int, message: string}
     */
    public function createManualBooking(array $data): array
    {
        $this->db->transStart();

        try {
            $booking = new Booking([
                'customer_id'         => (int) $data['customer_id'],
                'vehicle_id'          => (int) $data['vehicle_id'],
                'driver_id'           => (int) $data['driver_id'],
                'pickup_address'      => (string) $data['pickup_address'],
                'dropoff_address'     => (string) $data['dropoff_address'],
                'pickup_latitude'     => (float) $data['pickup_latitude'],
                'pickup_longitude'    => (float) $data['pickup_longitude'],
                'dropoff_latitude'    => (float) $data['dropoff_latitude'],
                'dropoff_longitude'   => (float) $data['dropoff_longitude'],
                'distance_km'         => (float) $data['distance_km'],
                'total_price'         => (float) $data['total_price'],
                'base_booking_fee'    => 0.00,
                'per_km_fuel_cost'    => 0.00,
                'maintenance_reserve' => 0.00,
                'driver_allowance'    => 0.00,
                'payment_status'      => (string) $data['payment_status'],
                'trip_status'         => 'pending',
                'paystack_reference'  => (string) ($data['paystack_reference'] ?? 'MANUAL-' . bin2hex(random_bytes(4))),
            ]);

            $this->bookingModel->insert($booking);
            $bookingId = (int) $this->db->insertID();

            // Set driver to on_trip if paid
            if (in_array($booking->payment_status, ['paid', 'manual_verified'], true)) {
                $driver = $this->driverModel->find($booking->driver_id);
                if ($driver !== null) {
                    $this->driverModel->update($driver->id, ['status' => 'on_trip']);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to create manual booking.');
            }

            return ['status' => true, 'booking_id' => $bookingId, 'message' => 'Manual booking #' . $bookingId . ' created successfully.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Manual Booking Failed', ['exception' => $e->getMessage()]);
            return ['status' => false, 'booking_id' => 0, 'message' => 'Failed to create manual booking.'];
        }
    }

    /**
     * Override payment status on a booking.
     *
     * @param int    $bookingId
     * @param string $paymentStatus
     * @param string $reference Optional Paystack reference
     *
     * @return array{status: bool, message: string}
     */
    public function overridePaymentStatus(int $bookingId, string $paymentStatus, string $reference = ''): array
    {
        /** @var Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking not found.'];
        }

        $booking->payment_status = $paymentStatus;

        if (! empty($reference)) {
            $booking->paystack_reference = $reference;
        }

        $this->bookingModel->update($booking->id, $booking);

        return ['status' => true, 'message' => 'Booking #' . $bookingId . ' payment status updated.'];
    }

    // --- Private Helper Methods ---

    /**
     * Handle driver status transitions when a booking is updated.
     */
    private function _handleDriverStatusTransition(
        string $oldStatus,
        string $newStatus,
        int $oldDriverId,
        int $newDriverId
    ): void {
        // If trip is being cancelled or completed, release the driver
        if (in_array($newStatus, ['cancelled', 'completed'], true) && $oldStatus === 'active') {
            $driver = $this->driverModel->find($oldDriverId);
            if ($driver !== null) {
                $this->driverModel->update($driver->id, ['status' => 'available']);
            }
        }

        // If trip is being activated, mark new driver as on_trip
        if ($newStatus === 'active' && $oldStatus !== 'active') {
            $newDriver = $this->driverModel->find($newDriverId);
            if ($newDriver !== null) {
                $this->driverModel->update($newDriver->id, ['status' => 'on_trip']);
            }
        }

        // If driver changed on an active trip, swap statuses
        if ($newStatus === 'active' && $oldDriverId !== $newDriverId) {
            $oldDriver = $this->driverModel->find($oldDriverId);
            if ($oldDriver !== null) {
                $this->driverModel->update($oldDriver->id, ['status' => 'available']);
            }
            $newDriver = $this->driverModel->find($newDriverId);
            if ($newDriver !== null) {
                $this->driverModel->update($newDriver->id, ['status' => 'on_trip']);
            }
        }
    }

    /**
     * Send payment confirmation email after successful booking.
     */
    private function _sendPaymentConfirmation(Booking $booking, int $bookingId): void
    {
        try {
            $userModel = new UserModel();
            /** @var \App\Modules\Auth\Entities\User|null $customer */
            $customer = $userModel->find($booking->customer_id);
            $customerEmail = $customer !== null ? $customer->email : 'customer@kongsafaris.com';
            $customerName  = $customer !== null ? $customer->getFullName() : 'Valued Customer';

            $emailService = new EmailService();
            $emailService->sendPaymentConfirmation(
                $customerEmail,
                $customerName,
                $bookingId,
                (float) $booking->total_price
            );
        } catch (\Throwable $err) {
            log_message('error', 'Failed to send payment confirmation email', [
                'booking_id' => $bookingId,
                'exception'  => $err->getMessage(),
            ]);
        }
    }
}