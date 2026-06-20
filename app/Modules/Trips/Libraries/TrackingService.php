<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\TripCoordinateModel;
use App\Modules\Trips\Entities\TripCoordinate;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;

/**
 * TrackingService
 *
 * Handles trip status transitions, GPS coordinate logging,
 * and coordinate retrieval for real-time driver tracking.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class TrackingService
{
    private ConnectionInterface $db;
    private BookingModel $bookingModel;
    private DriverModel $driverModel;
    private TripCoordinateModel $coordinateModel;

    private const MAX_COORDINATES_PER_BOOKING = 500;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->bookingModel = new BookingModel();
        $this->driverModel = new DriverModel();
        $this->coordinateModel = new TripCoordinateModel();
    }

    /**
     * Update trip status (start or complete a trip).
     *
     * @param int    $bookingId
     * @param string $status 'active' or 'completed'
     *
     * @return array{status: bool, message: string}
     */
    public function updateTripStatus(int $bookingId, string $status): array
    {
        if (! in_array($status, ['active', 'completed'], true)) {
            return ['status' => false, 'message' => 'Invalid status update request.'];
        }

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking record not found.'];
        }

        $this->db->transStart();

        try {
            $this->bookingModel->update($booking->id, ['trip_status' => $status]);

            if ($status === 'completed') {
                $this->driverModel->update($booking->driver_id, ['status' => 'available']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed while updating trip status.');
            }

            $msg = $status === 'active' ? 'Trip has started. Tracking active.' : 'Trip has successfully completed!';
            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Trip Status Update Failure', [
                'booking_id' => $bookingId,
                'status'     => $status,
                'exception'  => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'An error occurred during state transition.'];
        }
    }

    /**
     * Log GPS coordinates for an active trip.
     *
     * @param int   $bookingId
     * @param float $latitude
     * @param float $longitude
     *
     * @return array{status: bool, message: string}
     */
    public function logCoordinate(int $bookingId, float $latitude, float $longitude): array
    {
        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->select('id, trip_status')->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking record not found.'];
        }

        if ($booking->trip_status !== 'active') {
            return ['status' => false, 'message' => 'Coordinate logging rejected: Trip is not active.'];
        }

        $coordinate = new TripCoordinate([
            'booking_id' => $booking->id,
            'latitude'   => $latitude,
            'longitude'  => $longitude,
            'created_at' => Time::now()->toDateTimeString(),
        ]);

        $this->coordinateModel->insert($coordinate);

        return ['status' => true, 'message' => 'Coordinates updated.'];
    }

    /**
     * Retrieve coordinate history for a booking (capped for performance).
     *
     * @param int $bookingId
     *
     * @return array
     */
    public function getCoordinates(int $bookingId): array
    {
        return $this->coordinateModel
            ->select('latitude, longitude, created_at')
            ->where('booking_id', $bookingId)
            ->orderBy('created_at', 'ASC')
            ->findAll(self::MAX_COORDINATES_PER_BOOKING, 0);
    }

    /**
     * Find driver record linked to a user session.
     *
     * @param int $userId
     *
     * @return object|null Driver row
     */
    public function getDriverByUserId(int $userId): ?object
    {
        return $this->db->table('drivers')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
    }

    /**
     * Get active/pending bookings for a driver.
     *
     * @param int $driverId
     *
     * @return array
     */
    public function getDriverBookings(int $driverId): array
    {
        return $this->bookingModel
            ->select('bookings.*, vehicles.plate_number, vehicles.model')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->where('driver_id', $driverId)
            ->whereIn('trip_status', ['pending', 'active'])
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll();
    }
}