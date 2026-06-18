<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

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

        $result = service('authService')->authenticate($email, $password);

        if (! $result['status']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        /** @var \App\Modules\Auth\Entities\User $user */
        $user = $result['user'];

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

        $result = service('authService')->register($this->request->getPost());

        if (! $result['status']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }

        // Send verification email
        service('authService')->sendVerificationEmail(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('first_name'),
            $result['verification_token']
        );

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

        $result = service('authService')->verifyEmail($token);

        $method = $result['status'] ? 'success' : 'error';
        return redirect()->to(url_to('auth.login'))->with($method, $result['message']);
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

        $result = service('authService')->generateResetToken($email);

        // Only send email if user exists (prevents email enumeration)
        if ($result['user_exists'] && $result['reset_token'] !== null) {
            $userModel = new \App\Modules\Auth\Models\UserModel();
            /** @var \App\Modules\Auth\Entities\User|null $user */
            $user = $userModel->where('email', $email)->first();

            if ($user !== null) {
                service('authService')->sendPasswordResetEmail($email, $user->first_name, $result['reset_token']);
            }
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

        $result = service('authService')->resetPassword($token, (string) $this->request->getPost('password'));

        $method = $result['status'] ? 'success' : 'error';
        return redirect()->to(url_to('auth.login'))->with($method, $result['message']);
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

        $userModel = new \App\Modules\Auth\Models\UserModel();
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

        $result = service('authService')->updateProfile($userId, $this->request->getPost());

        if (! $result['status']) {
            return redirect()->to(url_to('auth.login'))->with('error', $result['message']);
        }

        /** @var \App\Modules\Auth\Entities\User $user */
        $user = $result['user'];

        // Update session data
        session()->set('first_name', $user->first_name);
        session()->set('last_name', $user->last_name);

        return redirect()->to(url_to('auth.profile'))
            ->with('success', 'Profile updated successfully.');
    }
}
