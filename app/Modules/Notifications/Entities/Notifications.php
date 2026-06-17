<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Entities;

use CodeIgniter\Entity\Entity;

class Notifications extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
