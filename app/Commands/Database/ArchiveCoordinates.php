<?php

declare(strict_types=1);

namespace App\Commands\Database;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * ArchiveCoordinates
 *
 * Moves trip coordinate records older than a specified retention
 * period to an archive table to keep the primary coordinates table
 * lean and performant.
 *
 * Usage: php spark db:archive-coordinates [--days=30]
 *
 * @package App\Commands\Database
 * @author Senior Developer
 * @since 1.0.0
 */
class ArchiveCoordinates extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:archive-coordinates';
    protected $description = 'Archives old trip coordinates to keep primary table performant.';
    protected $usage       = 'db:archive-coordinates [--days=30]';

    /**
     * Run the archive operation.
     *
     * @param array $params
     */
    public function run(array $params): void
    {
        $retentionDays = (int) CLI::getOption('days') ?: 30;
        $cutoffDate    = date('Y-m-d H:i:s', strtotime("-{$retentionDays} days"));

        $db = Database::connect();

        CLI::write("Archiving coordinates older than {$retentionDays} days (before {$cutoffDate})...", 'yellow');

        // Ensure the archive table exists
        $this->_ensureArchiveTable($db);

        // Count records to archive
        $count = $db->table('trip_coordinates')
            ->where('created_at <', $cutoffDate)
            ->countAllResults();

        if ($count === 0) {
            CLI::write('No coordinates to archive.', 'green');
            return;
        }

        CLI::write("Found {$count} records to archive.", 'info');

        // Move records to archive table in chunks of 500
        $chunkSize = 500;
        $moved     = 0;

        $db->transStart();

        try {
            do {
                $rows = $db->table('trip_coordinates')
                    ->select('*')
                    ->where('created_at <', $cutoffDate)
                    ->limit($chunkSize)
                    ->get()
                    ->getResultArray();

                if (empty($rows)) {
                    break;
                }

                // Batch insert into archive
                $db->table('trip_coordinates_archive')->insertBatch($rows);

                // Delete from primary table
                $ids = array_column($rows, 'id');
                $db->table('trip_coordinates')
                    ->whereIn('id', $ids)
                    ->delete();

                $moved += count($rows);
                CLI::write("Archived {$moved} / {$count}...", 'info');
            } while (count($rows) === $chunkSize);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Archive transaction failed.');
            }

            CLI::write("Successfully archived {$moved} coordinate records.", 'green');
        } catch (\Throwable $e) {
            $db->transRollback();
            CLI::error('Archive failed: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the archive table exists (mirrors trip_coordinates schema).
     *
     * @param mixed $db Database connection
     */
    private function _ensureArchiveTable($db): void
    {
        $forge = \Config\Database::forge();

        if (! $db->tableExists('trip_coordinates_archive')) {
            $forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'booking_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'latitude'   => ['type' => 'DECIMAL', 'constraint' => '10,7'],
                'longitude'  => ['type' => 'DECIMAL', 'constraint' => '10,7'],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $forge->addKey('id', true);
            $forge->addKey('booking_id', false, false, 'idx_archive_booking_id');
            $forge->createTable('trip_coordinates_archive', true);

            CLI::write('Created trip_coordinates_archive table.', 'info');
        }
    }
}