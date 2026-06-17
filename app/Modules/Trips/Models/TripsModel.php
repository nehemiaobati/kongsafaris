<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\Trips;

class TripsModel extends Model
{
    protected $table = 'trips_table';
    protected $returnType = Trips::class;
    protected $useTimestamps = true;
    protected $allowedFields = [];
}
