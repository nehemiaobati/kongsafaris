<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersAddAuthFields extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'email_verified_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'password_hash',
            ],
            'verification_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'email_verified_at',
            ],
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'verification_token',
            ],
            'reset_token_expires_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'reset_token',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', [
            'email_verified_at',
            'verification_token',
            'reset_token',
            'reset_token_expires_at',
        ]);
    }
}
