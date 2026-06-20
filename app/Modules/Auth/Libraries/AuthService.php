<?php

declare(strict_types=1);

namespace App\Modules\Auth\Libraries;

use App\Modules\Auth\Models\UserModel;
use App\Modules\Auth\Entities\User;
use App\Modules\Notifications\Libraries\EmailTemplateService;
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
    private EmailTemplateService $emailTemplateService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->emailTemplateService = new EmailTemplateService();
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

        return [
            'status'      => true,
            'reset_token' => $reset_token,
            'user_exists' => true,
            'first_name'  => $user->first_name,
        ];
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
     * Retrieve a user by ID.
     *
     * @param int $userId
     *
     * @return \App\Modules\Auth\Entities\User|null
     */
    public function getUserById(int $userId): ?\App\Modules\Auth\Entities\User
    {
        /** @var \App\Modules\Auth\Entities\User|null $user */
        return $this->userModel->find($userId);
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

            $body = $this->emailTemplateService->renderVerificationEmail($first_name, $verifyUrl);
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

            $body = $this->emailTemplateService->renderPasswordResetEmail($first_name, $resetUrl);
            $emailService->sendRawEmail($email, $subject, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Password reset email failed', ['exception' => $e->getMessage()]);
        }
    }
}
