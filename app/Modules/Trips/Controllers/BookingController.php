<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Libraries\BookingService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BookingController
 *
 * Handles booking lifecycle operations delegated to BookingService.
 * Controllers handle only validation + redirect logic.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class BookingController extends BaseController
{
    private BookingService $bookingService;

    public function __construct()
    {
        $this->bookingService = service('bookingService');
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

        $result = $this->bookingService->createManualBooking($this->request->getPost());

        if (! $result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', $result['message']);
    }

    /**
     * Cancel a pending trip (manager action).
     */
    public function cancelBooking(): ResponseInterface
    {
        $bookingId = (int) $this->request->getPost('booking_id');

        $result = $this->bookingService->cancelBooking($bookingId);

        if (! $result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', $result['message']);
    }

    /**
     * Force cancel any booking regardless of trip status.
     */
    public function forceCancelBooking(): ResponseInterface
    {
        $bookingId = (int) $this->request->getPost('booking_id');

        $result = $this->bookingService->cancelBooking($bookingId, true);

        if (! $result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', $result['message']);
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

        $bookingId = (int) $this->request->getPost('booking_id');

        $result = $this->bookingService->updateBooking($bookingId, $this->request->getPost());

        if (! $result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', $result['message']);
    }
}
