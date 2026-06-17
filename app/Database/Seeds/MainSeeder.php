<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MainSeeder extends Seeder
{
    public function run(): void
    {
        $db = $this->db;

        // Clean tables to start fresh
        $db->query('SET FOREIGN_KEY_CHECKS=0;');
        $db->table('trip_coordinates')->truncate();
        $db->table('bookings')->truncate();
        $db->table('fuel_rates')->truncate();
        $db->table('drivers')->truncate();
        $db->table('vehicles')->truncate();
        $db->table('users')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed Users
        $usersData = [
            [
                'email'         => 'admin@kongsafaris.com',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'first_name'    => 'David',
                'last_name'     => 'Kiprotich',
                'role'          => 'admin',
                'created_at'    => Time::now()->toDateTimeString(),
                'updated_at'    => Time::now()->toDateTimeString(),
            ],
            [
                'email'         => 'manager@kongsafaris.com',
                'password_hash' => password_hash('manager123', PASSWORD_BCRYPT),
                'first_name'    => 'Sarah',
                'last_name'     => 'Wanjiku',
                'role'          => 'manager',
                'created_at'    => Time::now()->toDateTimeString(),
                'updated_at'    => Time::now()->toDateTimeString(),
            ],
            [
                'email'         => 'driver@kongsafaris.com',
                'password_hash' => password_hash('driver123', PASSWORD_BCRYPT),
                'first_name'    => 'John',
                'last_name'     => 'Ouma',
                'role'          => 'driver',
                'created_at'    => Time::now()->toDateTimeString(),
                'updated_at'    => Time::now()->toDateTimeString(),
            ],
            [
                'email'         => 'customer@kongsafaris.com',
                'password_hash' => password_hash('customer123', PASSWORD_BCRYPT),
                'first_name'    => 'Mark',
                'last_name'     => 'Smith',
                'role'          => 'customer',
                'created_at'    => Time::now()->toDateTimeString(),
                'updated_at'    => Time::now()->toDateTimeString(),
            ],
        ];

        $db->table('users')->insertBatch($usersData);

        // Fetch user ids for references
        $managerId = (int)$db->table('users')->where('role', 'manager')->get()->getRow()->id;
        $driverUserId = (int)$db->table('users')->where('role', 'driver')->get()->getRow()->id;

        // 2. Seed Vehicles
        $vehiclesData = [
            [
                'plate_number'                => 'KAA 123A',
                'model'                       => 'Toyota Land Cruiser Safari 4x4',
                'fuel_efficiency'             => 8.0, // 8.0 Km per Liter
                'target_profit_margin_per_km' => 1.50, // Profit margin per Km
                'maintenance_reserve_per_km'  => 0.50, // Maintenance reserve per Km
                'status'                      => 'active',
                'created_at'                  => Time::now()->toDateTimeString(),
                'updated_at'                  => Time::now()->toDateTimeString(),
            ],
            [
                'plate_number'                => 'KAB 456B',
                'model'                       => 'Nissan Patrol Safari Caravan',
                'fuel_efficiency'             => 7.0, // 7.0 Km per Liter
                'target_profit_margin_per_km' => 1.80, // Profit margin per Km
                'maintenance_reserve_per_km'  => 0.60, // Maintenance reserve per Km
                'status'                      => 'active',
                'created_at'                  => Time::now()->toDateTimeString(),
                'updated_at'                  => Time::now()->toDateTimeString(),
            ],
        ];

        $db->table('vehicles')->insertBatch($vehiclesData);

        // 3. Seed Drivers
        $driverData = [
            'user_id'             => $driverUserId,
            'license_number'      => 'DL-998877',
            'allowance_flat_rate' => 50.00, // Flat allowance rate
            'status'              => 'available',
            'created_at'          => Time::now()->toDateTimeString(),
            'updated_at'          => Time::now()->toDateTimeString(),
        ];

        $db->table('drivers')->insert($driverData);

        // 4. Seed Fuel Rates
        $fuelRateData = [
            'price_per_liter' => 1.45, // $1.45 per liter baseline price
            'updated_by'      => $managerId,
            'created_at'      => Time::now()->toDateTimeString(),
        ];

        $db->table('fuel_rates')->insert($fuelRateData);
    }
}
