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

    /**
     * Completed trips analysis — technically realized gains.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getCompletedTripsAnalysis(string $start_date, string $end_date): array
    {
        $row = $this->db->table('bookings b')
            ->select("
                COUNT(b.id) as completed_trips,
                SUM(b.distance_km) as completed_distance,
                SUM(b.total_price) as completed_gross_revenue,
                SUM(b.per_km_fuel_cost + b.maintenance_reserve + b.driver_allowance) as completed_total_costs,
                SUM(b.total_price) - SUM(b.per_km_fuel_cost + b.maintenance_reserve + b.driver_allowance) as completed_net_profit,
                AVG(b.distance_km) as avg_distance,
                AVG(b.total_price) as avg_trip_value
            ")
            ->where('b.trip_status', 'completed')
            ->where('b.created_at >=', $start_date)
            ->where('b.created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        if ($row === null) {
            $row = [
                'completed_trips'       => 0,
                'completed_distance'    => 0,
                'completed_gross_revenue' => 0,
                'completed_total_costs' => 0,
                'completed_net_profit'  => 0,
                'avg_distance'          => 0,
                'avg_trip_value'        => 0,
            ];
        }

        return $row;
    }

    /**
     * Uncompleted trips analysis — technically unrealized gains.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getUncompletedTripsAnalysis(string $start_date, string $end_date): array
    {
        $row = $this->db->table('bookings')
            ->select("
                COUNT(id) as uncompleted_trips,
                SUM(CASE WHEN trip_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_trips,
                SUM(CASE WHEN trip_status = 'pending' THEN 1 ELSE 0 END) as pending_trips,
                SUM(CASE WHEN trip_status = 'active' THEN 1 ELSE 0 END) as active_trips,
                SUM(total_price) as unrealized_potential_revenue,
                SUM(CASE WHEN payment_status IN ('paid', 'manual_verified') AND trip_status != 'completed' THEN total_price ELSE 0 END) as at_risk_revenue
            ")
            ->where('trip_status !=', 'completed')
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        if ($row === null) {
            $row = [
                'uncompleted_trips'         => 0,
                'cancelled_trips'           => 0,
                'pending_trips'             => 0,
                'active_trips'              => 0,
                'unrealized_potential_revenue' => 0,
                'at_risk_revenue'           => 0,
            ];
        }

        return $row;
    }

    /**
     * Refund analysis — financially realized losses.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function getRefundAnalysis(string $start_date, string $end_date): array
    {
        $row = $this->db->table('bookings')
            ->select("
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_trips,
                SUM(CASE WHEN payment_status = 'refunded' THEN total_price ELSE 0 END) as total_refunded,
                AVG(CASE WHEN payment_status = 'refunded' THEN total_price ELSE NULL END) as avg_refund_amount,
                COUNT(CASE WHEN payment_status = 'refund_requested' THEN 1 END) as pending_refund_requests
            ")
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        if ($row === null) {
            $row = [
                'refunded_trips'          => 0,
                'total_refunded'          => 0,
                'avg_refund_amount'       => 0,
                'pending_refund_requests' => 0,
            ];
        }

        // Calculate refund rate against gross revenue
        $summary = $this->getOverallSummary($start_date, $end_date);
        $grossRevenue = (float) ($summary['total_revenue'] ?? 0);
        $totalRefunded = (float) ($row['total_refunded'] ?? 0);

        $row['refund_rate'] = $grossRevenue > 0 ? round(($totalRefunded / $grossRevenue) * 100, 2) : 0;

        return $row;
    }
}
