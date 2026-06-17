<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use CodeIgniter\Model;
use App\Modules\Auth\Entities\Auth;

class AuthModel extends Model
{
    protected $table = 'auth_table';
    protected $returnType = Auth::class;
    protected $useTimestamps = true;
    protected $allowedFields = [];
}
