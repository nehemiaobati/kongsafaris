<?php

declare(strict_types=1);

namespace App\Modules\Auth\Libraries;

use App\Modules\Auth\Models\UserModel;
use App\Modules\Auth\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * AuthService
 *
 * Encapsulates all authentication business logic: login, registration,
 * email verification, password reset, and profile management.
 *
 * @package App\Modules\Auth\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class AuthService
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return array{status: bool, user: User|null, message: string}
     */
    public function authenticate(string $email, string $password): array
    {
        /** @var User|null $user */
        $user = $this->userModel->select('id, email, password_hash, first_name, last_name, role')
            ->where('email', $email)
            ->first();

        if ($user === null || ! password_verify($password, $user->password_hash)) {
            return ['status' => false, 'user' => null, 'message' => 'Invalid email or password.'];
        }

        return ['status' => true, 'user' => $user, 'message' => ''];
    }

    /**
     * Register a new customer user with verification token.
     *
     * @param array $data Must include: first_name, last_name, email, password
     *
     * @return array{status: bool, user_id: int, verification_token: string, message: string}
     */
    public function register(array $data): array
    {
        $user = new User([
            'email'      => $data['email'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'role'       => 'customer',
        ]);
        $user->setPassword($data['password']);

        $verification_token = bin2hex(random_bytes(32));
        $user->verification_token = $verification_token;

        $this->userModel->insert($user);
        $user_id = (int) $this->userModel->getInsertID();

        return [
            'status'             => true,
            'user_id'            => $user_id,
            'verification_token' => $verification_token,
            'message'            => '',
        ];
    }

    /**
     * Verify email address via token.
     *
     * @param string $token
     *
     * @return array{status: bool, message: string}
     */
    public function verifyEmail(string $token): array
    {
        /** @var User|null $user */
        $user = $this->userModel->where('verification_token', $token)->first();

        if ($user === null) {
            return ['status' => false, 'message' => 'Invalid or expired verification link.'];
        }

        $user->verification_token = null;
        $user->email_verified_at = Time::now()->toDateTimeString();
        $this->userModel->update($user->id, $user);

        return ['status' => true, 'message' => 'Email verified successfully! You can now log in.'];
    }

    /**
     * Generate password reset token.
     *
     * @param string $email
     *
     * @return array{status: bool, reset_token: string|null, user_exists: bool}
     */
    public function generateResetToken(string $email): array
    {
        /** @var User|null $user */
        $user = $this->userModel->where('email', $email)->first();

        if ($user === null) {
            return ['status' => true, 'reset_token' => null, 'user_exists' => false];
        }

        $reset_token = bin2hex(random_bytes(32));
        $user->reset_token = $reset_token;
        $user->reset_token_expires_at = Time::now()->addMinutes(60)->toDateTimeString();
        $this->userModel->update($user->id, $user);

        return ['status' => true, 'reset_token' => $reset_token, 'user_exists' => true];
    }

    /**
     * Validate and execute password reset.
     *
     * @param string $token
     * @param string $new_password
     *
     * @return array{status: bool, message: string}
     */
    public function resetPassword(string $token, string $new_password): array
    {
        /** @var User|null $user */
        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_token_expires_at >=', Time::now()->toDateTimeString())
            ->first();

        if ($user === null) {
            return ['status' => false, 'message' => 'Invalid or expired reset link.'];
        }

        $user->setPassword($new_password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $this->userModel->update($user->id, $user);

        return ['status' => true, 'message' => 'Password reset successfully! You can now log in with your new password.'];
    }

    /**
     * Update user profile.
     *
     * @param int    $user_id
     * @param array  $data Must include: first_name, last_name, password (optional)
     *
     * @return array{status: bool, message: string, user: User|null}
     */
    public function updateProfile(int $user_id, array $data): array
    {
        /** @var User|null $user */
        $user = $this->userModel->find($user_id);

        if ($user === null) {
            return ['status' => false, 'message' => 'User not found.', 'user' => null];
        }

        $user->first_name = $data['first_name'];
        $user->last_name  = $data['last_name'];

        if (! empty($data['password'])) {
            $user->setPassword($data['password']);
        }

        $this->userModel->update($user_id, $user);

        return ['status' => true, 'message' => 'Profile updated successfully.', 'user' => $user];
    }

    /**
     * Send a verification email.
     *
     * @param string $email
     * @param string $first_name
     * @param string $token
     *
     * @return void
     */
    public function sendVerificationEmail(string $email, string $first_name, string $token): void
    {
        try {
            $emailService = service('emailService');
            $verifyUrl = site_url('auth/register/verify/' . $token);
            $subject = 'Verify your Kong Safaris account';

            $body = $this->_buildVerificationEmailBody($first_name, $verifyUrl);
            $emailService->sendRawEmail($email, $subject, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Registration verification email failed', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a password reset email.
     *
     * @param string $email
     * @param string $first_name
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetEmail(string $email, string $first_name, string $token): void
    {
        try {
            $emailService = service('emailService');
            $resetUrl = site_url('auth/reset-password/' . $token);
            $subject = 'Reset your Kong Safaris password';

            $body = $this->_buildPasswordResetEmailBody($first_name, $resetUrl);
            $emailService->sendRawEmail($email, $subject, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Password reset email failed', ['exception' => $e->getMessage()]);
        }
    }

    // --- Private Helper Methods ---

    /**
     * Build verification email HTML.
     */
    private function _buildVerificationEmailBody(string $first_name, string $verifyUrl): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                    <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                    <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                    <p>Dear " . esc($first_name) . ",</p>
                    <p>Thank you for creating an account. Please click the button below to verify your email address.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . $verifyUrl . "' style='background-color: #d4af37; color: #121813; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600;'>Verify Email Address</a>
                    </div>
                    <p style='font-size: 0.85em; color: #8c9c90;'>If you did not create this account, please ignore this email.</p>
                    <p style='font-size: 0.9em; color: #8c9c90;'>Best Regards,<br>Kong Safaris Operations Team</p>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Build password reset email HTML.
     */
    private function _buildPasswordResetEmailBody(string $first_name, string $resetUrl): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                    <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                    <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                    <p>Dear " . esc($first_name) . ",</p>
                    <p>You requested a password reset. Click the button below to set a new password. This link expires in 60 minutes.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . $resetUrl . "' style='background-color: #d4af37; color: #121813; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600;'>Reset Password</a>
                    </div>
                    <p style='font-size: 0.85em; color: #8c9c90;'>If you did not request this, please ignore this email.</p>
                    <p style='font-size: 0.9em; color: #8c9c90;'>Best Regards,<br>Kong Safaris Operations Team</p>
                </div>
            </body>
            </html>
        ";
    }
}
