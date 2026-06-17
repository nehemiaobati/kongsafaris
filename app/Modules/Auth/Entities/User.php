<?php

declare(strict_types=1);

namespace App\Modules\Auth\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $datamap = [];
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    protected $casts = [
        'id'            => 'integer',
        'email'         => 'string',
        'password_hash' => 'string',
        'first_name'    => 'string',
        'last_name'     => 'string',
        'role'          => 'string',
    ];

    /**
     * Set password mutator
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * Get full name accessor
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
