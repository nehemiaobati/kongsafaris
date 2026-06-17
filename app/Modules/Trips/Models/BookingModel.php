<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\Booking;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $returnType = Booking::class;
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $allowedFields = [
        'customer_id',
        'vehicle_id',
        'driver_id',
        'pickup_address',
        'dropoff_address',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_latitude',
        'dropoff_longitude',
        'distance_km',
        'base_booking_fee',
        'per_km_fuel_cost',
        'maintenance_reserve',
        'driver_allowance',
        'total_price',
        'payment_status',
        'trip_status',
        'paystack_reference',
    ];
}
