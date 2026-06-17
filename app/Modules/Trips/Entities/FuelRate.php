<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class FuelRate extends Entity
{
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'              => 'integer',
        'price_per_liter' => 'float',
        'updated_by'      => 'integer',
    ];
}
