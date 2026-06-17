<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class Driver extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'id'                  => 'integer',
        'user_id'             => 'integer',
        'license_number'      => 'string',
        'allowance_flat_rate' => 'float',
        'status'              => 'string',
    ];
}
