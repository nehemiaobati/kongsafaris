<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class Booking extends Entity
{
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'id'                  => 'integer',
        'customer_id'         => 'integer',
        'vehicle_id'          => 'integer',
        'driver_id'           => 'integer',
        'pickup_address'      => 'string',
        'dropoff_address'     => 'string',
        'pickup_latitude'     => 'float',
        'pickup_longitude'    => 'float',
        'dropoff_latitude'    => 'float',
        'dropoff_longitude'   => 'float',
        'distance_km'         => 'float',
        'base_booking_fee'    => 'float',
        'per_km_fuel_cost'    => 'float',
        'maintenance_reserve' => 'float',
        'driver_allowance'    => 'float',
        'total_price'         => 'float',
        'payment_status'      => 'string',
        'trip_status'         => 'string',
        'paystack_reference'  => 'string',
    ];
}
