<?php

declare(strict_types=1);

namespace App\Modules\Trips\Models;

use CodeIgniter\Model;
use App\Modules\Trips\Entities\SystemSetting;

class SystemSettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $returnType = SystemSetting::class;

    protected $useTimestamps = true;
    protected $createdField  = 'updated_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'updated_by',
    ];
}
