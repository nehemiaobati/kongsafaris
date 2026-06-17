<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TripsController extends BaseController
{
    public function index(): string|ResponseInterface
    {
        return view('App\Modules\Trips\Views\index', ['pageTitle' => 'Trips Module']);
    }
}
