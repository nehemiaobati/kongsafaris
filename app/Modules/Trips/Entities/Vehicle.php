<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class Vehicle extends Entity
{
    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'id'                          => 'integer',
        'plate_number'                => 'string',
        'model'                       => 'string',
        'fuel_type'                   => 'string',
        'fuel_efficiency'             => 'float',
        'target_profit_margin_per_km' => 'float',
        'maintenance_reserve_per_km'  => 'float',
        'status'                      => 'string',
    ];
}
