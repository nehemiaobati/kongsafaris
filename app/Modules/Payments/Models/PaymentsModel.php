<?php

declare(strict_types=1);

namespace App\Modules\Payments\Models;

use CodeIgniter\Model;
use App\Modules\Payments\Entities\Payments;

class PaymentsModel extends Model
{
    protected $table = 'payments_table';
    protected $returnType = Payments::class;
    protected $useTimestamps = true;
    protected $allowedFields = [];
}
