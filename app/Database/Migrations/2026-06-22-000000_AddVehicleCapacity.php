<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add capacity column to vehicles table.
 *
 * @package App\Database\Migrations
 * @author Senior Developer
 * @since 1.0.0
 */
class AddVehicleCapacity extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vehicles', [
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 4,
                'after'      => 'maintenance_reserve_per_km',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vehicles', 'capacity');
    }
}
