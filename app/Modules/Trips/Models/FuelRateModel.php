<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\FuelRate;

class FuelRateModel extends Model
{
    protected $table = 'fuel_rates';
    protected $primaryKey = 'id';
    protected $returnType = FuelRate::class;
    
    protected $useTimestamps = false;
    
    protected $allowedFields = [
        'price_per_liter',
        'updated_by',
        'created_at',
    ];
}
