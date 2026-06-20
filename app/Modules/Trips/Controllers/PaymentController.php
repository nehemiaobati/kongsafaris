<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Auth\Models\UserModel;
use App\Modules\Payments\Libraries\PaystackService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PaymentController
 *
 * Handles payment operations: initiation, refunds, and status overrides.
 * Extracted from ManagerDashboardController to enforce single responsibility.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class PaymentController extends BaseController
{
    private BookingModel $bookingModel;
    private VehicleModel $vehicleModel;
    private DriverModel $driverModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
    }

    /**
     * Process refund requests via Paystack or manual clearance.
     */
    public function processRefund(): ResponseInterface
    {
        $booking_id = (int) $this->request->getPost('booking_id');
        $action     = (string) $this->request->getPost('action');

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($booking_id);

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
            $this->bookingModel->update($booking->id, $booking);

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
     * Allow manager to initiate payment collection for pending bookings (or retry failed).
     */
    public function initiatePayment(): ResponseInterface
    {
        $rules = [
            'booking_id' => 'required|integer',
            'provider'   => 'required|in_list[card,mpesa,airtel]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $booking_id = (int) $this->request->getPost('booking_id');
        $provider   = (string) $this->request->getPost('provider');

        /** @var \App\Modules\Trips\Entities\Booking|null $booking */
        $booking = $this->bookingModel->find($booking_id);

        if ($booking === null) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        if (! in_array($booking->payment_status, ['pending', 'failed'], true)) {
            return redirect()->back()->with('error', 'Payment can only be collected for pending or failed payments.');
        }

        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $this->vehicleModel->find((int)$booking->vehicle_id);
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $this->driverModel->find((int)$booking->driver_id);

        if ($vehicle === null || $driver === null) {
            return redirect()->back()->with('error', 'Vehicle or driver configuration missing for this booking.');
        }

        $customer_id = (int)$booking->customer_id;
        $userModel   = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $customer */
        $customer = $userModel->find($customer_id);
        $email    = $customer !== null ? (string)$customer->email : 'customer@kongsafaris.com';

        $paystackService = service('paystackService');

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

    /**
     * Override payment status on a booking (mark as paid manually).
     */
    public function overridePaymentStatus(): ResponseInterface
    {
        $rules = [
            'booking_id'       => 'required|integer',
            'payment_status'   => 'required|in_list[pending,paid,failed,manual_verified,refund_requested,refunded]',
            'paystack_reference' => 'permit_empty|string',
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

        $booking->payment_status = (string) $this->request->getPost('payment_status');

        $reference = (string) $this->request->getPost('paystack_reference');
        if (! empty($reference)) {
            $booking->paystack_reference = $reference;
        }

        $this->bookingModel->update($booking->id, $booking);

        return redirect()->to(url_to('trips.manager'))
            ->with('success', 'Booking #' . $booking_id . ' payment status updated to ' . $booking->payment_status . '.');
    }
}