<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthMiddleware
 *
 * Centralized authorization filter for role-based access control.
 * Eliminates duplicated permission checks across controllers.
 *
 * Usage in routes:
 *   'filter' => 'auth:manager,admin'
 *   'filter' => 'auth:customer'
 *   'filter' => 'auth:manager,admin,customer'
 *
 * @package App\Filters
 * @author Senior Developer
 * @since 1.0.0
 */
class AuthMiddleware implements FilterInterface
{
    /**
     * Execute the filter before the controller.
     *
     * @param RequestInterface $request The incoming request
     * @param array|null       $arguments Allowed roles (e.g., ['manager', 'admin'])
     *
     * @return RequestInterface|ResponseInterface|string|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check authentication
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(url_to('auth.login'));
        }

        // If no roles specified, just require login
        if (empty($arguments)) {
            return null;
        }

        // Check role authorization
        $userRole = session()->get('role');

        if (! in_array($userRole, $arguments, true)) {
            // Redirect to appropriate dashboard based on role
            return $this->_redirectByRole($userRole);
        }

        return null;
    }

    /**
     * Execute the filter after the controller.
     *
     * @param RequestInterface  $request  The incoming request
     * @param ResponseInterface $response The outgoing response
     * @param array|null        $arguments Arguments passed to filter
     *
     * @return ResponseInterface|null
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    // --- Helper Methods ---

    /**
     * Redirect user to their role-specific dashboard.
     *
     * @param string|null $role
     *
     * @return ResponseInterface
     */
    private function _redirectByRole(?string $role): ResponseInterface
    {
        return match ($role) {
            'customer' => redirect()->to(url_to('trips.customer.dashboard')),
            'manager', 'admin' => redirect()->to(url_to('trips.manager')),
            'driver' => redirect()->to(url_to('trips.driver')),
            default => redirect()->to(url_to('auth.login')),
        };
    }
}