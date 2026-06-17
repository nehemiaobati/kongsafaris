<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Controllers\BaseController;
use App\Modules\Auth\Models\UserModel;
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

        $email    = (string)$this->request->getPost('email');
        $password = (string)$this->request->getPost('password');

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
        return view('App\Modules\Auth\Views\dashboard', [
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
}
