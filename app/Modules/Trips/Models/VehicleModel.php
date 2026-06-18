<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\Vehicle;

class VehicleModel extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    protected $returnType = Vehicle::class;

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'plate_number',
        'model',
        'fuel_type',
        'fuel_efficiency',
        'target_profit_margin_per_km',
        'maintenance_reserve_per_km',
        'status',
    ];
}
