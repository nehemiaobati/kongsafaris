<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Libraries;

/**
 * EmailTemplateService
 *
 * Manages email template rendering to separate presentation
 * from business logic in service classes.
 *
 * @package App\Modules\Notifications\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class EmailTemplateService
{
    private const BRAND_NAME = 'KONG SAFARIS';
    private const BRAND_COLORS = [
        'primary' => '#0d6efd',
        'background' => '#ffffff',
        'text' => '#212529',
        'muted' => '#6c757d',
    ];

    /**
     * Render a verification email template.
     */
    public function renderVerificationEmail(string $first_name, string $verifyUrl): string
    {
        return $this->_renderBaseTemplate(
            'Verify your email address',
            "Dear {$first_name},<br>Thank you for creating an account. Please click the button below to verify your email address.",
            'Verify Email Address',
            $verifyUrl,
            'If you did not create this account, please ignore this email.'
        );
    }

    /**
     * Render a password reset email template.
     */
    public function renderPasswordResetEmail(string $first_name, string $resetUrl): string
    {
        return $this->_renderBaseTemplate(
            'Reset your password',
            "Dear {$first_name},<br>You requested a password reset. Click the button below to set a new password. This link expires in 60 minutes.",
            'Reset Password',
            $resetUrl,
            'If you did not request this, please ignore this email.'
        );
    }

    // --- Private Helper Methods ---

    /**
     * Base email template wrapper.
     */
    private function _renderBaseTemplate(string $title, string $body, string $buttonText, string $buttonUrl, string $footerNote): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #ffffff; color: #212529; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #0d6efd; border-radius: 10px; padding: 30px;'>
                    <h2 style='color: #0d6efd; text-align: center;'>" . self::BRAND_NAME . "</h2>
                    <hr style='border: 0; border-top: 1px solid #0d6efd; opacity: 0.3;'>
                    <p>{$body}</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$buttonUrl}' style='background-color: #0d6efd; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600;'>{$buttonText}</a>
                    </div>
                    <p style='font-size: 0.85em; color: #6c757d;'>{$footerNote}</p>
                    <p style='font-size: 0.9em; color: #6c757d;'>Best Regards,<br>" . self::BRAND_NAME . " Operations Team</p>
                </div>
            </body>
            </html>
        ";
    }
}
