<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class NotificationsController extends BaseController
{
    public function index(): string|ResponseInterface
    {
        return view('App\Modules\Notifications\Views\index', ['pageTitle' => 'Notifications Module']);
    }
}
