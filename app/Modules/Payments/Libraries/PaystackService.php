<?php

declare(strict_types=1);

namespace App\Modules\Payments\Libraries;

use App\Modules\Trips\Entities\Booking;
use CodeIgniter\HTTP\CURLRequest;

class PaystackService
{
    private string $secret_key;
    private string $api_url = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secret_key = (string)env('PAYSTACK_SECRET_KEY');
    }

    /**
     * Initialize standard checkout transaction (Cards, Banks, etc.)
     *
     * @param float  $price
     * @param string $email
     * @param array  $metadata
     *
     * @return array{status: bool, authorization_url?: string, reference?: string, message?: string}
     */
    public function initializeTransaction(float $price, string $email, array $metadata): array
    {
        $reference = 'KONG_P_' . bin2hex(random_bytes(6));
        $callback_url = base_url('payments/callback');

        $payload = [
            'email'        => $email,
            'amount'       => (int)round($price * 100),
            'reference'    => $reference,
            'callback_url' => $callback_url,
            'metadata'     => $metadata,
        ];

        try {
            /** @var CURLRequest $client */
            $client = \Config\Services::curlrequest();
            $response = $client->post($this->api_url . '/transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secret_key,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]);

            $status_code = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status_code === 200 && ($body['status'] ?? false)) {
                return [
                    'status'            => true,
                    'authorization_url' => $body['data']['authorization_url'],
                    'reference'         => $reference,
                ];
            }

            return [
                'status'  => false,
                'message' => $body['message'] ?? 'Failed to initialize Paystack transaction.',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Paystack Transaction Init Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Connection to payment gateway failed.',
            ];
        }
    }

    /**
     * Initialize Mobile Money Transaction (Safaricom M-Pesa or Airtel Money)
     *
     * Uses POST /transaction/initialize with channels=['mobile_money'] and
     * metadata custom_filters to restrict the Paystack hosted checkout to the
     * selected provider. Paystack handles the STK push natively on its page.
     *
     * @param float  $price
     * @param string $email
     * @param string $provider ('mpesa' or 'airtel')
     * @param array  $metadata
     *
     * @return array{status: bool, authorization_url?: string, reference?: string, message?: string}
     */
    public function initializeMobileMoneyCharge(float $price, string $email, string $provider, array $metadata): array
    {
        $reference = 'KONG_P_' . bin2hex(random_bytes(6));
        $callback_url = base_url('payments/callback');

        // Embed the custom filters inside the metadata payload
        $metadata['custom_filters'] = [
            'supported_mobile_money_providers' => [$provider],
        ];

        $payload = [
            'email'        => $email,
            'amount'       => (int)round($price * 100),
            'reference'    => $reference,
            'callback_url' => $callback_url,
            'channels'     => ['mobile_money'],
            'metadata'     => $metadata,
        ];

        try {
            /** @var CURLRequest $client */
            $client = \Config\Services::curlrequest();
            $response = $client->post($this->api_url . '/transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secret_key,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]);

            $status_code = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status_code === 200 && ($body['status'] ?? false)) {
                return [
                    'status'            => true,
                    'authorization_url' => $body['data']['authorization_url'],
                    'reference'         => $reference,
                ];
            }

            return [
                'status'  => false,
                'message' => $body['message'] ?? 'Failed to initialize mobile money transaction.',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Paystack Mobile Money Init Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Connection to payment gateway failed.',
            ];
        }
    }

    /**
     * Query Paystack's validation endpoint to verify transaction
     *
     * @param string $reference
     *
     * @return array{status: bool, booking_id?: int, amount?: float, message?: string}
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            /** @var CURLRequest $client */
            $client = \Config\Services::curlrequest();
            $response = $client->get($this->api_url . '/transaction/verify/' . rawurlencode($reference), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secret_key,
                    'Accept'        => 'application/json',
                ],
                'http_errors' => false,
            ]);

            $status_code = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status_code === 200 && ($body['status'] ?? false) && $body['data']['status'] === 'success') {
                $metadata = $body['data']['metadata'] ?? [];
                $booking_id = isset($metadata['booking_id']) ? (int)$metadata['booking_id'] : 0;
                $amount = (float)($body['data']['amount'] / 100.0);

                return [
                    'status'     => true,
                    'booking_id' => $booking_id,
                    'amount'     => $amount,
                    'metadata'   => $metadata,
                ];
            }

            return [
                'status'  => false,
                'message' => $body['message'] ?? 'Transaction was not successfully paid.',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Paystack Verify Transaction Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Verification server communication failed.',
            ];
        }
    }

    /**
     * Process Paystack Refund
     *
     * @param string $reference
     * @param float  $amount
     *
     * @return array{status: bool, message?: string}
     */
    public function initiateRefund(string $reference, float $amount): array
    {
        $payload = [
            'transaction' => $reference,
            'amount'      => (int)round($amount * 100),
        ];

        try {
            /** @var CURLRequest $client */
            $client = \Config\Services::curlrequest();
            $response = $client->post($this->api_url . '/refund', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secret_key,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]);

            $status_code = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($status_code === 200 && ($body['status'] ?? false)) {
                return [
                    'status' => true,
                ];
            }

            return [
                'status'  => false,
                'message' => $body['message'] ?? 'Refund initialization failed on payment processor.',
            ];

        } catch (\Throwable $e) {
            log_message('error', 'Paystack Refund Request Failure: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gateway communication failed during refund.',
            ];
        }
    }
}
