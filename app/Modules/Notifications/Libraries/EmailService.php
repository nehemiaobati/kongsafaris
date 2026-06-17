<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Libraries;

use Config\Email as EmailConfig;

class EmailService
{
    private array $config;

    public function __construct()
    {
        // Read configs dynamically from environment
        $this->config = [
            'userAgent' => 'CodeIgniter',
            'protocol'  => 'smtp',
            'SMTPHost'  => (string)env('email.SMTPHost'),
            'SMTPUser'  => (string)env('email.SMTPUser'),
            'SMTPPass'  => (string)env('email.SMTPPass'),
            'SMTPPort'  => (int)(env('email.SMTPPort') ?? 587),
            'SMTPCrypto'=> (string)(env('email.SMTPCrypto') ?? 'tls'),
            'mailType'  => (string)(env('email.mailType') ?? 'html'),
            'charset'   => 'UTF-8',
            'wordWrap'  => true,
        ];
    }

    /**
     * Send transactional HTML email for payment confirmation
     *
     * @param string $to
     * @param string $customer_name
     * @param int    $booking_id
     * @param float  $amount
     *
     * @return bool
     */
    public function sendPaymentConfirmation(string $to, string $customer_name, int $booking_id, float $amount): bool
    {
        $subject = 'Payment Confirmed! Safari Booking #' . $booking_id;
        $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                    <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                    <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                    <p>Dear " . esc($customer_name) . ",</p>
                    <p>We are excited to confirm that your payment of <strong>$" . number_format($amount, 2) . "</strong> has been successfully processed for booking <strong>#" . $booking_id . "</strong>.</p>
                    <p>Your safari transfer is now fully paid and scheduled. You can track progress or review details on your customer dashboard.</p>
                    <br>
                    <p style='font-size: 0.9em; color: #8c9c90;'>Thank you for choosing Kong Safaris.<br><em>Explore Africa in comfort.</em></p>
                </div>
            </body>
            </html>
        ";

        return $this->_sendEmail($to, $subject, $body);
    }

    /**
     * Send transactional HTML email for trip status updates
     *
     * @param string $to
     * @param string $customer_name
     * @param int    $booking_id
     * @param string $status
     *
     * @return bool
     */
    public function sendTripStatusUpdate(string $to, string $customer_name, int $booking_id, string $status): bool
    {
        $subject = 'Trip Status Update: Booking #' . $booking_id;
        $body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                    <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                    <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                    <p>Dear " . esc($customer_name) . ",</p>
                    <p>Your safari booking <strong>#" . $booking_id . "</strong> status has been updated to: <strong style='text-transform: uppercase; color: #d4af37;'>" . esc($status) . "</strong>.</p>
                    <p>Our fleet operations coordinates are updating in real-time.</p>
                    <br>
                    <p style='font-size: 0.9em; color: #8c9c90;'>Best Regards,<br>Kong Safaris Operations Team</p>
                </div>
            </body>
            </html>
        ";

        return $this->_sendEmail($to, $subject, $body);
    }

    /**
     * Internal procedural method to fire SMTP transmission
     */
    private function _sendEmail(string $to, string $subject, string $body): bool
    {
        $email = \Config\Services::email();
        $email->initialize($this->config);

        $fromEmail = (string)env('email.fromEmail');
        $fromName  = (string)env('email.fromName');

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($body);

        if ($email->send()) {
            return true;
        }

        // Log SMTP debugger trace on failure state
        log_message('error', 'SMTP Transmission Failure: ' . $email->printDebugger(['headers', 'subject', 'body']));
        return false;
    }
}
