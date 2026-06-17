<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class TripCoordinate extends Entity
{
    protected $dates = ['created_at'];
    
    protected $casts = [
        'id'         => 'integer',
        'booking_id' => 'integer',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];
}
