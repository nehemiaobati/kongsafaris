<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemSettings extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'setting_value' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->addKey('updated_by');
        $this->forge->createTable('system_settings', true);

        // Insert defaults
        $db = \Config\Database::connect();
        $db->table('system_settings')->insertBatch([
            ['setting_key' => 'base_booking_fee', 'setting_value' => '50.00', 'updated_by' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'system_name', 'setting_value' => 'Kong Safaris', 'updated_by' => 1, 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('system_settings', true);
    }
}
