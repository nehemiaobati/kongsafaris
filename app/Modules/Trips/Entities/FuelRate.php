<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class FuelRate extends Entity
{
    protected $dates = ['created_at'];

    protected $casts = [
        'id'              => 'integer',
        'fuel_type'       => 'string',
        'price_per_liter' => 'float',
        'updated_by'      => 'integer',
    ];

    protected $hidden = ['updated_by'];
}
