<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\TripCoordinate;

class TripCoordinateModel extends Model
{
    protected $table = 'trip_coordinates';
    protected $primaryKey = 'id';
    protected $returnType = TripCoordinate::class;
    
    protected $useTimestamps = false;
    
    protected $allowedFields = [
        'booking_id',
        'latitude',
        'longitude',
        'created_at',
    ];
}
