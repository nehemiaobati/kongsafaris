<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        return redirect()->to(url_to('auth.login'));
    }
}
