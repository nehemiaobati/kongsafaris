<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentsController extends BaseController
{
    public function index(): string|ResponseInterface
    {
        return view('App\Modules\Payments\Views\index', ['pageTitle' => 'Payments Module']);
    }
}
