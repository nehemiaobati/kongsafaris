<?php

declare(strict_types=1);

namespace App\Modules\Auth\Entities;

use CodeIgniter\Entity\Entity;

class Auth extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
