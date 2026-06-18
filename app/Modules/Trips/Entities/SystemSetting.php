<?php

declare(strict_types=1);

namespace App\Modules\Trips\Entities;

use CodeIgniter\Entity\Entity;

class SystemSetting extends Entity
{
    protected $dates = ['updated_at'];

    protected $casts = [
        'id'            => 'integer',
        'setting_key'   => 'string',
        'setting_value' => 'string',
        'updated_by'    => 'integer',
    ];
}
