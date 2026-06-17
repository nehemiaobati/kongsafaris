<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Controllers\BaseController;
use App\Modules\Auth\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    /**
     * Render the login screen
     */
    public function loginView(): string|ResponseInterface
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.dashboard'));
        }

        return view('App\Modules\Auth\Views\login', [
            'pageTitle'       => 'Login | Kong Safaris Operations',
            'metaDescription' => 'Log in to the Kong Safaris Fleet Operations & Booking Management System.',
            'canonicalUrl'    => url_to('auth.login'),
            'robotsTag'       => 'noindex, nofollow',
        ]);
    }

    /**
     * Process authentication request
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();

        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->select('id, email, password_hash, first_name, last_name, role')
            ->where('email', $email)
            ->first();

        if ($user === null || ! password_verify($password, $user->password_hash)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        // Establish User Session
        $sessionData = [
            'userId'     => $user->id,
            'email'      => $user->email,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'role'       => $user->role,
            'isLoggedIn' => true,
        ];
        session()->set($sessionData);

        return redirect()->to(url_to('auth.dashboard'))
            ->with('success', 'Logged in successfully. Welcome back, ' . $user->first_name . '!');
    }

    /**
     * Dashboard home routing based on session role
     */
    public function dashboard(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        $role = session()->get('role');

        if ($role === 'customer') {
            return redirect()->to(url_to('trips.customer.dashboard'));
        }

        if ($role === 'manager' || $role === 'admin') {
            return redirect()->to(url_to('trips.manager'));
        }

        if ($role === 'driver') {
            return redirect()->to(url_to('trips.driver'));
        }

        // Fallback
        return view('App\Modules\Auth\Views\index', [
            'pageTitle'       => 'Dashboard | Kong Safaris Operations',
            'metaDescription' => 'Kong Safaris Fleet Operations & Booking Management System dashboard.',
            'canonicalUrl'    => url_to('auth.dashboard'),
            'robotsTag'       => 'noindex, nofollow',
        ]);
    }

    /**
     * Terminate user session
     */
    public function logout(): ResponseInterface
    {
        session()->destroy();
        return redirect()->to(url_to('auth.login'))
            ->with('success', 'You have been successfully logged out.');
    }

    // --- Registration & Email Verification ---

    /**
     * Render the registration screen
     */
    public function registerView(): string|ResponseInterface
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.dashboard'));
        }

        return view('App\Modules\Auth\Views\register', [
            'pageTitle'       => 'Create Account | Kong Safaris',
            'metaDescription' => 'Register for a Kong Safaris account to book safaris and track trips.',
            'canonicalUrl'    => url_to('auth.register'),
            'robotsTag'       => 'noindex, nofollow',
        ]);
    }

    /**
     * Process customer registration
     */
    public function register(): ResponseInterface
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $user = new \App\Modules\Auth\Entities\User([
            'email'      => (string) $this->request->getPost('email'),
            'first_name' => (string) $this->request->getPost('first_name'),
            'last_name'  => (string) $this->request->getPost('last_name'),
            'role'       => 'customer',
        ]);
        $user->setPassword((string) $this->request->getPost('password'));

        // Generate verification token
        $verification_token = bin2hex(random_bytes(32));
        $user->verification_token = $verification_token;

        $userModel->insert($user);

        // Send verification email
        try {
            $emailService = service('emailService');
            $verifyUrl = site_url('auth/register/verify/' . $verification_token);

            $subject = 'Verify your Kong Safaris account';
            $body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                        <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                        <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                        <p>Dear " . esc($user->first_name) . ",</p>
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

            $emailService->sendRawEmail((string) $user->email, $subject, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Registration verification email failed', [
                'user_id' => $userModel->getInsertID(),
                'exception' => $e->getMessage(),
            ]);
        }

        return redirect()->to(url_to('auth.login'))
            ->with('success', 'Account created! Please check your email to verify your account.');
    }

    /**
     * Verify email address via token
     */
    public function verifyEmail(string $token): ResponseInterface
    {
        if (empty($token)) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Invalid verification link.');
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->where('verification_token', $token)->first();

        if ($user === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Invalid or expired verification link.');
        }

        $user->verification_token = null;
        $user->email_verified_at = Time::now()->toDateTimeString();
        $userModel->update($user->id, $user);

        return redirect()->to(url_to('auth.login'))
            ->with('success', 'Email verified successfully! You can now log in.');
    }

    // --- Password Reset ---

    /**
     * Render forgot password screen
     */
    public function forgotPasswordView(): string|ResponseInterface
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.dashboard'));
        }

        return view('App\Modules\Auth\Views\forgot_password', [
            'pageTitle'       => 'Forgot Password | Kong Safaris',
            'metaDescription' => 'Reset your Kong Safaris account password.',
            'canonicalUrl'    => url_to('auth.forgot'),
            'robotsTag'       => 'noindex, nofollow',
        ]);
    }

    /**
     * Process forgot password request
     */
    public function forgotPassword(): ResponseInterface
    {
        $rules = ['email' => 'required|valid_email'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = (string) $this->request->getPost('email');

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->where('email', $email)->first();

        // Always return success to prevent email enumeration
        if ($user === null) {
            return redirect()->to(url_to('auth.login'))
                ->with('success', 'If that email exists, a password reset link has been sent.');
        }

        $reset_token = bin2hex(random_bytes(32));
        $user->reset_token = $reset_token;
        $user->reset_token_expires_at = Time::now()->addMinutes(60)->toDateTimeString();
        $userModel->update($user->id, $user);

        // Send reset email
        try {
            $resetUrl = site_url('auth/reset-password/' . $reset_token);
            $emailService = service('emailService');

            $subject = 'Reset your Kong Safaris password';
            $body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #121813; color: #f1f3f2; padding: 20px;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #1a231b; border: 1px solid #d4af37; border-radius: 10px; padding: 30px;'>
                        <h2 style='color: #d4af37; text-align: center;'>🦁 KONG SAFARIS</h2>
                        <hr style='border: 0; border-top: 1px solid #d4af37; opacity: 0.3;'>
                        <p>Dear " . esc($user->first_name) . ",</p>
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

            $emailService->sendRawEmail($email, $subject, $body);
        } catch (\Throwable $e) {
            log_message('error', 'Password reset email failed', ['exception' => $e->getMessage()]);
        }

        return redirect()->to(url_to('auth.login'))
            ->with('success', 'If that email exists, a password reset link has been sent.');
    }

    /**
     * Render reset password form
     */
    public function resetPasswordView(string $token): string|ResponseInterface
    {
        if (empty($token)) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Invalid reset link.');
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->where('reset_token', $token)
            ->where('reset_token_expires_at >=', Time::now()->toDateTimeString())
            ->first();

        if ($user === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Invalid or expired reset link.');
        }

        return view('App\Modules\Auth\Views\reset_password', [
            'pageTitle'       => 'Reset Password | Kong Safaris',
            'metaDescription' => 'Set a new password for your Kong Safaris account.',
            'canonicalUrl'    => site_url('auth/reset-password/' . $token),
            'robotsTag'       => 'noindex, nofollow',
            'token'           => $token,
        ]);
    }

    /**
     * Process password reset
     */
    public function resetPassword(string $token): ResponseInterface
    {
        $rules = [
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->where('reset_token', $token)
            ->where('reset_token_expires_at >=', Time::now()->toDateTimeString())
            ->first();

        if ($user === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'Invalid or expired reset link.');
        }

        $user->setPassword((string) $this->request->getPost('password'));
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $userModel->update($user->id, $user);

        return redirect()->to(url_to('auth.login'))
            ->with('success', 'Password reset successfully! You can now log in with your new password.');
    }

    // --- Customer Profile ---

    /**
     * Render customer profile screen
     */
    public function profileView(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->find(session()->get('userId'));

        if ($user === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'User not found.');
        }

        return view('App\Modules\Auth\Views\profile', [
            'pageTitle'       => 'My Profile | Kong Safaris',
            'metaDescription' => 'Update your Kong Safaris account profile.',
            'canonicalUrl'    => url_to('auth.profile'),
            'robotsTag'       => 'noindex, nofollow',
            'user'            => $user,
        ]);
    }

    /**
     * Process profile update
     */
    public function profileUpdate(): ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        $userId = (int) session()->get('userId');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
        ];

        // Only validate password if provided
        $password = (string) $this->request->getPost('password');
        if (! empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->find($userId);

        if ($user === null) {
            return redirect()->to(url_to('auth.login'))->with('error', 'User not found.');
        }

        $user->first_name = (string) $this->request->getPost('first_name');
        $user->last_name  = (string) $this->request->getPost('last_name');

        if (! empty($password)) {
            $user->setPassword($password);
        }

        $userModel->update($userId, $user);

        // Update session data
        session()->set('first_name', $user->first_name);
        session()->set('last_name', $user->last_name);

        return redirect()->to(url_to('auth.profile'))
            ->with('success', 'Profile updated successfully.');
    }
}
