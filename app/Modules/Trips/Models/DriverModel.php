<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\Driver;

class DriverModel extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'id';
    protected $returnType = Driver::class;
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $allowedFields = [
        'user_id',
        'license_number',
        'allowance_flat_rate',
        'status',
    ];
}
