<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Models;

use CodeIgniter\Model;
use App\Modules\Notifications\Entities\Notifications;

class NotificationsModel extends Model
{
    protected $table = 'notifications_table';
    protected $returnType = Notifications::class;
    protected $useTimestamps = true;
    protected $allowedFields = [];
}
