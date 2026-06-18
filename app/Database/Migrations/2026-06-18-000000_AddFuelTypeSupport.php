<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFuelTypeSupport extends Migration
{
    public function up(): void
    {
        // Add fuel_type to fuel_rates table
        if (! $this->db->fieldExists('fuel_type', 'fuel_rates')) {
            $this->forge->addColumn('fuel_rates', [
                'fuel_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['petrol', 'diesel'],
                    'default'    => 'petrol',
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey(['fuel_type', 'created_at']);
        }

        // Add fuel_type to vehicles table
        if (! $this->db->fieldExists('fuel_type', 'vehicles')) {
            $this->forge->addColumn('vehicles', [
                'fuel_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['petrol', 'diesel'],
                    'default'    => 'petrol',
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('fuel_type');
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fuel_type', 'fuel_rates')) {
            $this->forge->dropColumn('fuel_rates', 'fuel_type');
        }
        if ($this->db->fieldExists('fuel_type', 'vehicles')) {
            $this->forge->dropColumn('vehicles', 'fuel_type');
        }
    }
}
