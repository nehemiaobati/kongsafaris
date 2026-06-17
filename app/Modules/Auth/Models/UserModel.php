<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use CodeIgniter\Model;
use App\Modules\Auth\Entities\User;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = User::class;
    
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    protected $allowedFields = [
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'role',
    ];
    
    protected $validationRules = [
        'email'      => 'required|valid_email|is_unique[users.email,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'role'       => 'required|in_list[admin,manager,driver,customer]',
    ];
}
