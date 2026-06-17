<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * ReportService
 *
 * Aggregates booking, vehicle, and driver data for reporting dashboards.
 * All queries use explicit column selection (SELECT * is forbidden).
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class ReportService
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Revenue summary by vehicle for a date range.
     *
     * @param string $start_date YYYY-MM-DD
     * @param string $end_date   YYYY-MM-DD
     *
     * @return array
     */
    public function getRevenueByVehicle(string $start_date, string $end_date): array
    {
        return $this->db->table('bookings b')
            ->select("
                v.id as vehicle_id,
                v.model,
                v.plate_number,
                COUNT(b.id) as trip_count,
                SUM(b.distance_km) as total_km,
                SUM(b.total_price) as gross_revenue,
                SUM(b.per_km_fuel_cost) as total_fuel,
                SUM(b.maintenance_reserve) as total_maintenance,
                SUM(b.driver_allowance) as total_allowances
            ")
            ->join('vehicles v', 'v.id = b.vehicle_id')
            ->whereIn('b.payment_status', ['paid', 'manual_verified'])
            ->where('b.created_at >=', $start_date)
            ->where('b.created_at <=', $end_date . ' 23:59:59')
            ->groupBy('v.id')
            ->orderBy('gross_revenue', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Revenue summary by driver for a date range.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getRevenueByDriver(string $start_date, string $end_date): array
    {
        return $this->db->table('bookings b')
            ->select("
                d.id as driver_id,
                u.first_name,
                u.last_name,
                COUNT(b.id) as trip_count,
                SUM(b.distance_km) as total_km,
                SUM(b.total_price) as gross_revenue,
                SUM(b.driver_allowance) as total_allowances
            ")
            ->join('drivers d', 'd.id = b.driver_id')
            ->join('users u', 'u.id = d.user_id')
            ->whereIn('b.payment_status', ['paid', 'manual_verified'])
            ->where('b.created_at >=', $start_date)
            ->where('b.created_at <=', $end_date . ' 23:59:59')
            ->groupBy('d.id')
            ->orderBy('gross_revenue', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Revenue trend (daily totals) for a date range.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getRevenueTrend(string $start_date, string $end_date): array
    {
        return $this->db->table('bookings')
            ->select("
                DATE(created_at) as date,
                COUNT(id) as trip_count,
                SUM(total_price) as daily_revenue
            ")
            ->whereIn('payment_status', ['paid', 'manual_verified'])
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Monthly fuel cost summary.
     *
     * @return array
     */
    public function getFuelCostTrend(): array
    {
        return $this->db->table('fuel_rates')
            ->select("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                price_per_liter,
                updated_by
            ")
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Overall summary statistics.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getOverallSummary(string $start_date, string $end_date): array
    {
        $row = $this->db->table('bookings')
            ->select("
                COUNT(id) as total_trips,
                SUM(distance_km) as total_distance,
                SUM(total_price) as total_revenue,
                SUM(per_km_fuel_cost) as total_fuel_cost,
                SUM(maintenance_reserve) as total_maintenance,
                SUM(driver_allowance) as total_allowances,
                COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_trips,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_trips
            ")
            ->whereIn('payment_status', ['paid', 'manual_verified', 'refunded'])
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        if ($row === null) {
            $row = [];
        }

        return $row;
    }
}
