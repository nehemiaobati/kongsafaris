<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Controllers\BaseController;
use App\Modules\Auth\Models\UserModel;
use App\Modules\Auth\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminController
 *
 * Handles full user management CRUD for admin role.
 * Admin can manage all roles: admin, manager, driver, customer.
 *
 * @package App\Modules\Auth\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class AdminController extends BaseController
{
    /**
     * List all users with search/filter.
     */
    public function users(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        $search = (string) $this->request->getGet('search');
        $roleFilter = (string) $this->request->getGet('role');

        $userModel = new UserModel();
        $query = $userModel->newQuery();

        if (!empty($search)) {
            $query->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if (!empty($roleFilter) && in_array($roleFilter, ['admin', 'manager', 'driver', 'customer'], true)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('created_at', 'DESC')->findAll();

        return view('App\Modules\Auth\Views\admin_users', [
            'pageTitle'       => 'User Management | Admin',
            'metaDescription' => 'Manage system users and role assignments.',
            'canonicalUrl'    => url_to('auth.admin.users'),
            'robotsTag'       => 'noindex, nofollow',
            'users'           => $users,
            'search'          => $search,
            'roleFilter'      => $roleFilter,
        ]);
    }

    /**
     * Show create user form.
     */
    public function createUserView(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        return view('App\Modules\Auth\Views\admin_user_form', [
            'pageTitle'       => 'Create User | Admin',
            'metaDescription' => 'Create a new system user.',
            'canonicalUrl'    => url_to('auth.admin.create_user'),
            'robotsTag'       => 'noindex, nofollow',
            'mode'            => 'create',
            'user'            => null,
        ]);
    }

    /**
     * Process user creation.
     */
    public function createUser(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        $rules = [
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'email'               => 'required|valid_email|is_unique[users.email]',
            'password'            => 'required|min_length[6]',
            'role'                => 'required|in_list[admin,manager,driver,customer]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = new User($this->request->getPost());
        $user->setPassword((string) $this->request->getPost('password'));

        $userModel = new UserModel();
        $userModel->insert($user);

        return redirect()->to(url_to('auth.admin.users'))->with('success', 'User created successfully.');
    }

    /**
     * Show edit user form.
     */
    public function editUserView(int $id): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to(url_to('auth.admin.users'))->with('error', 'User not found.');
        }

        return view('App\Modules\Auth\Views\admin_user_form', [
            'pageTitle'       => 'Edit User | Admin',
            'metaDescription' => 'Edit system user details.',
            'canonicalUrl'    => url_to('auth.admin.edit_user', ['id' => $id]),
            'robotsTag'       => 'noindex, nofollow',
            'mode'            => 'edit',
            'user'            => $user,
        ]);
    }

    /**
     * Process user update.
     */
    public function editUser(int $id): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        $userModel = new UserModel();
        /** @var \App\Modules\Auth\Entities\User|null $user */
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to(url_to('auth.admin.users'))->with('error', 'User not found.');
        }

        $rules = [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'       => 'required|in_list[admin,manager,driver,customer]',
        ];

        if (!empty($this->request->getPost('password'))) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user->fill($this->request->getPost());

        if (!empty($this->request->getPost('password'))) {
            $user->setPassword((string) $this->request->getPost('password'));
        }

        $userModel->update($id, $user);

        return redirect()->to(url_to('auth.admin.users'))->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $id): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(url_to('auth.login'))->with('error', 'Access denied.');
        }

        // Prevent self-deletion
        if ($id === (int) session()->get('userId')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to(url_to('auth.admin.users'))->with('error', 'User not found.');
        }

        $userModel->delete($id);

        return redirect()->to(url_to('auth.admin.users'))->with('success', 'User deleted successfully.');
    }
}
