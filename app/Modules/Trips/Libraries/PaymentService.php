<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Auth\Models\UserModel;
use App\Modules\Payments\Libraries\PaystackService;
use App\Modules\Trips\Entities\Booking;
use CodeIgniter\Database\ConnectionInterface;

/**
 * PaymentService
 *
 * Encapsulates payment operations: refund processing, payment initiation,
 * and payment status overrides. Controllers MUST delegate all payment
 * business logic to this service.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class PaymentService
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
     * Process a refund via Paystack or manual clearance.
     *
     * @param int    $bookingId
     * @param string $action 'refund_paystack' or 'manual'
     *
     * @return array{status: bool, message: string}
     */
    public function processRefund(int $bookingId, string $action): array
    {
        /** @var Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null || $booking->payment_status !== 'refund_requested') {
            return ['status' => false, 'message' => 'Invalid booking selected for refund.'];
        }

        $this->db->transStart();

        try {
            if ($action === 'refund_paystack') {
                /** @var PaystackService $paystackService */
                $paystackService = service('paystackService');
                $refund = $paystackService->initiateRefund(
                    $booking->paystack_reference ?? '',
                    (float) $booking->total_price
                );

                if (! $refund['status']) {
                    return ['status' => false, 'message' => 'Paystack Refund Error: ' . ($refund['message'] ?? 'Unknown error.')];
                }
            }

            $booking->payment_status = 'refunded';
            $this->bookingModel->update($booking->id, $booking);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to save refund status.');
            }

            return ['status' => true, 'message' => 'Refund cleared successfully.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Refund Processing Failure', [
                'booking_id' => $bookingId,
                'exception'  => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Failed to process refund.'];
        }
    }

    /**
     * Initialize payment for a booking via Paystack.
     *
     * @param int    $bookingId
     * @param string $provider 'card', 'mpesa', or 'airtel'
     *
     * @return array{status: bool, authorization_url?: string, message: string}
     */
    public function initiatePayment(int $bookingId, string $provider): array
    {
        /** @var Booking|null $booking */
        $booking = $this->bookingModel->find($bookingId);

        if ($booking === null) {
            return ['status' => false, 'message' => 'Booking not found.'];
        }

        if (! in_array($booking->payment_status, ['pending', 'failed'], true)) {
            return ['status' => false, 'message' => 'Payment can only be collected for pending or failed payments.'];
        }

        $vehicle = $this->vehicleModel->find((int) $booking->vehicle_id);
        $driver  = $this->driverModel->find((int) $booking->driver_id);

        if ($vehicle === null || $driver === null) {
            return ['status' => false, 'message' => 'Vehicle or driver configuration missing for this booking.'];
        }

        $customerId = (int) $booking->customer_id;
        $userModel  = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $customer */
        $customer = $userModel->find($customerId);
        $email    = $customer !== null ? (string) $customer->email : 'customer@kongsafaris.com';

        $metadata = [
            'customer_id'         => $customerId,
            'booking_id'          => $bookingId,
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

        /** @var PaystackService $paystackService */
        $paystackService = service('paystackService');

        if ($provider === 'card') {
            $init = $paystackService->initializeTransaction(
                (float) $booking->total_price,
                $email,
                $metadata
            );
        } else {
            $init = $paystackService->initializeMobileMoneyCharge(
                (float) $booking->total_price,
                $email,
                $provider,
                $metadata
            );
        }

        if (! $init['status']) {
            return [
                'status'  => false,
                'message' => 'Payment initialization failed: ' . ($init['message'] ?? 'Unknown error.'),
            ];
        }

        if (! empty($init['authorization_url'])) {
            return [
                'status'            => true,
                'authorization_url' => $init['authorization_url'],
                'message'           => 'Redirecting to payment.',
            ];
        }

        return [
            'status'  => false,
            'message' => 'Payment initialized but no redirect URL was returned.',
        ];
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

        return [
            'status'  => true,
            'message' => 'Booking #' . $bookingId . ' payment status updated to ' . $paymentStatus . '.',
        ];
    }
}