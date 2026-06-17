<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\TripCoordinateModel;
use App\Modules\Trips\Entities\TripCoordinate;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * DriverTrackingController
 *
 * Handles driver workspace display, trip status transitions,
 * and real-time GPS coordinate updates.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class DriverTrackingController extends BaseController
{
    /**
     * Driver workspace panel listing assigned bookings.
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
     * Start or complete a trip.
     */
    public function updateTripStatus(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'driver') {
            return redirect()->to(url_to('auth.login'));
        }

        $booking_id = (int) $this->request->getPost('booking_id');
        $status     = (string) $this->request->getPost('status');

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
            log_message('error', 'Trip Status Update Failure', [
                'booking_id' => $booking_id,
                'status'     => $status,
                'exception'  => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'An error occurred during state transition.');
        }
    }

    /**
     * AJAX endpoint to log GPS coordinates from driver browser.
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

        $booking_id = (int) $this->request->getPost('booking_id');
        $lat        = (float) $this->request->getPost('latitude');
        $lng        = (float) $this->request->getPost('longitude');

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
                'message'    => 'Coordinate logging rejected: Trip is not active.',
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
     * AJAX fetch coordinate history for manager tracking map modal.
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
}