<?php

declare(strict_types=1);

namespace App\Modules\Payments\Entities;

use CodeIgniter\Entity\Entity;

class Payments extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
