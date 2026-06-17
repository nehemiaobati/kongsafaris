<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterBookingsPaymentStatus extends Migration
{
    public function up(): void
    {
        $this->forge->modifyColumn('bookings', [
            'payment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'default'    => 'pending',
                'null'       => false,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->modifyColumn('bookings', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'manual_verified'],
                'default'    => 'pending',
                'null'       => false,
            ],
        ]);
    }
}
