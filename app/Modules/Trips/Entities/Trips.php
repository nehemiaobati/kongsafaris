<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class Trips extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
