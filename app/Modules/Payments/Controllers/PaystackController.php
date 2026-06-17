<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Entities\Booking;
use App\Modules\Trips\Libraries\PricingService;
use App\Modules\Auth\Models\UserModel;
use App\Modules\Payments\Libraries\PaystackService;
use App\Modules\Notifications\Libraries\EmailService;
use CodeIgniter\HTTP\ResponseInterface;

class PaystackController extends BaseController
{
    /**
     * AJAX endpoint to initiate checkout payment (Standard or M-Pesa STK)
     * Calculated dynamically and stored in Paystack transaction metadata before redirecting.
     */
    public function checkout(): ResponseInterface
    {
        $rules = [
            'vehicle_id'        => 'required|integer',
            'driver_id'         => 'required|integer',
            'pickup_address'    => 'required|string',
            'dropoff_address'   => 'required|string',
            'pickup_latitude'   => 'required|numeric',
            'pickup_longitude'  => 'required|numeric',
            'dropoff_latitude'  => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'provider'          => 'required|in_list[card,mpesa,airtel]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'     => 'validation_error',
                'message'    => 'Invalid checkout request details.',
                'result'     => [],
                'errors'     => $this->validator->getErrors(),
                'csrf_token' => csrf_hash(),
            ]);
        }

        $vehicle_id      = (int)$this->request->getPost('vehicle_id');
        $driver_id       = (int)$this->request->getPost('driver_id');
        $pickup_address  = (string)$this->request->getPost('pickup_address');
        $dropoff_address = (string)$this->request->getPost('dropoff_address');
        
        $p_lat = (float)$this->request->getPost('pickup_latitude');
        $p_lng = (float)$this->request->getPost('pickup_longitude');
        $d_lat = (float)$this->request->getPost('dropoff_latitude');
        $d_lng = (float)$this->request->getPost('dropoff_longitude');
        $provider   = (string)$this->request->getPost('provider');

        $vehicleModel = new VehicleModel();
        $driverModel  = new DriverModel();

        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $vehicleModel->find($vehicle_id);
        
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find($driver_id);

        if ($vehicle === null || $driver === null) {
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Selected vehicle or driver profile is not available.',
                'result'     => [],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        // Calculate pricing
        $distance_km = $this->_getGoogleDistance($p_lat, $p_lng, $d_lat, $d_lng);
        $pricingService = new PricingService();
        $pricing = $pricingService->calculateQuote($vehicle, $driver, $distance_km);

        $customer_id = session()->get('userId');
        $email = session()->get('email') ?? 'customer@kongsafaris.com';

        // Prepare the payload that will be stored in Paystack metadata
        $metadata = [
            'customer_id'         => $customer_id,
            'vehicle_id'          => $vehicle->id,
            'driver_id'           => $driver->id,
            'pickup_address'      => $pickup_address,
            'dropoff_address'     => $dropoff_address,
            'pickup_latitude'     => $p_lat,
            'pickup_longitude'    => $p_lng,
            'dropoff_latitude'    => $d_lat,
            'dropoff_longitude'   => $d_lng,
            'distance_km'         => $distance_km,
            'base_booking_fee'    => $pricing['base_booking_fee'],
            'per_km_fuel_cost'    => $pricing['per_km_fuel_cost'],
            'maintenance_reserve' => $pricing['maintenance_reserve'],
            'driver_allowance'    => $pricing['driver_allowance'],
            'total_price'         => $pricing['total_price'],
        ];

        $paystackService = new PaystackService();

        if ($provider === 'card') {
            $init = $paystackService->initializeTransaction($pricing['total_price'], $email, $metadata);
        } else {
            $init = $paystackService->initializeMobileMoneyCharge($pricing['total_price'], $email, $provider, $metadata);
        }

        if ($init['status']) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Paystack transaction initialized.',
                'result'  => [
                    'authorization_url' => $init['authorization_url'],
                    'reference'         => $init['reference'],
                ],
                'errors'     => [],
                'csrf_token' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'     => 'error',
            'message'    => $init['message'] ?? 'Unable to contact Paystack API.',
            'result'     => [],
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * GET Redirect Callback Endpoint from Paystack
     */
    public function callback(): ResponseInterface
    {
        $reference = (string)$this->request->getGet('reference');

        if (empty($reference)) {
            return redirect()->to(url_to('auth.dashboard'))->with('error', 'Payment reference not found.');
        }

        $paystackService = new PaystackService();
        $verification = $paystackService->verifyTransaction($reference);

        if ($verification['status']) {
            $booking_id = $this->_getOrCreateBooking($reference, $verification['metadata']);

            if ($booking_id > 0) {
                return redirect()->to(url_to('auth.dashboard'))
                    ->with('success', 'Thank you! Your payment has been confirmed and booking is complete.');
            }
        }

        return redirect()->to(url_to('auth.dashboard'))
            ->with('error', 'Payment verification failed: ' . ($verification['message'] ?? 'Invalid payload.'));
    }

    /**
     * POST Webhook listener endpoint
     */
    public function webhook(): ResponseInterface
    {
        $signature = (string)$this->request->getHeaderLine('x-paystack-signature');
        $payload   = (string)$this->request->getBody();

        if (empty($signature) || empty($payload)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Missing signature headers.',
            ]);
        }

        // Validate webhook signature securely using HMAC SHA512
        $key = (string)env('PAYSTACK_SECRET_KEY');
        $expected = hash_hmac('sha512', $payload, $key);

        if ($signature !== $expected) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Signature mismatch verification.',
            ]);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        if ($event === 'charge.success') {
            $reference = $data['data']['reference'] ?? '';
            $metadata = $data['data']['metadata'] ?? [];

            if (!empty($reference)) {
                $this->_getOrCreateBooking($reference, $metadata);
            }
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Webhook event processed.',
            'result'     => [],
            'errors'     => [],
            'csrf_token' => csrf_hash(),
        ]);
    }

    /**
     * Create the booking in DB from Paystack metadata if not already created (idempotency check)
     */
    private function _getOrCreateBooking(string $reference, array $metadata): int
    {
        if (empty($metadata)) {
            return 0;
        }

        $bookingModel = new BookingModel();
        
        // Idempotency: Check if this reference was already processed
        $existing = $bookingModel->where('paystack_reference', $reference)->first();
        if ($existing !== null) {
            return (int)$existing->id;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $booking = new Booking([
                'customer_id'         => isset($metadata['customer_id']) ? (int)$metadata['customer_id'] : null,
                'vehicle_id'          => (int)$metadata['vehicle_id'],
                'driver_id'           => (int)$metadata['driver_id'],
                'pickup_address'      => (string)$metadata['pickup_address'],
                'dropoff_address'     => (string)$metadata['dropoff_address'],
                'pickup_latitude'     => (float)$metadata['pickup_latitude'],
                'pickup_longitude'    => (float)$metadata['pickup_longitude'],
                'dropoff_latitude'    => (float)$metadata['dropoff_latitude'],
                'dropoff_longitude'   => (float)$metadata['dropoff_longitude'],
                'distance_km'         => (float)$metadata['distance_km'],
                'base_booking_fee'    => (float)$metadata['base_booking_fee'],
                'per_km_fuel_cost'    => (float)$metadata['per_km_fuel_cost'],
                'maintenance_reserve' => (float)$metadata['maintenance_reserve'],
                'driver_allowance'    => (float)$metadata['driver_allowance'],
                'total_price'         => (float)$metadata['total_price'],
                'payment_status'      => 'paid',
                'trip_status'         => 'pending',
                'paystack_reference'  => $reference,
            ]);

            $bookingModel->insert($booking);
            $booking_id = (int)$db->insertID();

            // Set driver status to 'on_trip'
            $driverModel = new DriverModel();
            /** @var \App\Modules\Trips\Entities\Driver|null $driver */
            $driver = $driverModel->find($booking->driver_id);
            if ($driver !== null) {
                // To avoid "There is no data to update" error in CI4 Entity, use an array update on the model directly
                $driverModel->update($driver->id, ['status' => 'on_trip']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed on post-payment booking creation.');
            }

            // Trigger Email Notifications
            try {
                $userModel = new UserModel();
                /** @var \App\Modules\Auth\Entities\User|null $customer */
                $customer = $userModel->find($booking->customer_id);
                $customer_email = $customer !== null ? $customer->email : 'customer@kongsafaris.com';
                $customer_name  = $customer !== null ? $customer->getFullName() : 'Valued Customer';

                $emailService = new EmailService();
                $emailService->sendPaymentConfirmation(
                    $customer_email,
                    $customer_name,
                    $booking_id,
                    (float)$booking->total_price
                );
            } catch (\Throwable $err) {
                log_message('error', 'Failed to send notification emails: ' . $err->getMessage());
            }

            return $booking_id;

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('critical', 'Callback Booking Insertion Exception: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compute distance between coordinates using Google Matrix API or Haversine fallback.
     */
    private function _getGoogleDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $apiKey = env('GoogleMaps.APIKey');
        if (! empty($apiKey)) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$lat1},{$lng1}&destinations={$lat2},{$lng2}&key={$apiKey}";
            
            try {
                $client = \Config\Services::curlrequest();
                $response = $client->get($url, [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 5,
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                        // Value returns in meters, convert to Km
                        return (float)$data['rows'][0]['elements'][0]['distance']['value'] / 1000.0;
                    }
                }
            } catch (\Throwable $t) {
                log_message('error', 'Google Distance API Request Failed: ' . $t->getMessage());
            }
        }

        // Fallback: Haversine distance * 1.3 (approximation of road routing distance)
        return $this->_calculateHaversineDistance($lat1, $lng1, $lat2, $lng2) * 1.3;
    }

    /**
     * Calculates distance using the Haversine formula
     */
    private function _calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
