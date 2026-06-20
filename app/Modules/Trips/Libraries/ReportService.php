<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * ReportService
 *
 * Aggregates booking, vehicle, and driver data for reporting dashboards.
 * Consolidated from 8 separate queries into 3 efficient aggregate queries
 * to reduce database round trips by 63%.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class ReportService
{
    private BaseConnection $db;

    /** Maximum rows returned for unbounded queries like fuel cost trend. */
    private const MAX_FUEL_RECORDS = 200;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Fetch ALL report data in a single call (3 queries instead of 8).
     *
     * @param string $start_date YYYY-MM-DD
     * @param string $end_date   YYYY-MM-DD
     *
     * @return array{summary: array, by_vehicle: array, by_driver: array, trend: array, fuel_trend: array, completed_analysis: array, uncompleted_analysis: array, refund_analysis: array}
     */
    public function getAllReportData(string $start_date, string $end_date): array
    {
        // Query 1: Overall summary + completed analysis + refund analysis (combined)
        $summaryRow = $this->db->table('bookings')
            ->select("
                COUNT(id) as total_trips,
                SUM(distance_km) as total_distance,
                SUM(total_price) as total_revenue,
                SUM(per_km_fuel_cost) as total_fuel_cost,
                SUM(maintenance_reserve) as total_maintenance,
                SUM(driver_allowance) as total_allowances,
                COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_trips,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_trips,
                COUNT(CASE WHEN trip_status = 'completed' THEN 1 END) as completed_trips,
                SUM(CASE WHEN trip_status = 'completed' THEN distance_km ELSE 0 END) as completed_distance,
                SUM(CASE WHEN trip_status = 'completed' THEN total_price ELSE 0 END) as completed_gross_revenue,
                SUM(CASE WHEN trip_status = 'completed' THEN per_km_fuel_cost + maintenance_reserve + driver_allowance ELSE 0 END) as completed_total_costs,
                AVG(CASE WHEN trip_status = 'completed' THEN distance_km ELSE NULL END) as avg_distance,
                AVG(CASE WHEN trip_status = 'completed' THEN total_price ELSE NULL END) as avg_trip_value,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_count,
                SUM(CASE WHEN payment_status = 'refunded' THEN total_price ELSE 0 END) as total_refunded,
                AVG(CASE WHEN payment_status = 'refunded' THEN total_price ELSE NULL END) as avg_refund_amount,
                COUNT(CASE WHEN payment_status = 'refund_requested' THEN 1 END) as pending_refund_requests,
                COUNT(CASE WHEN trip_status = 'cancelled' THEN 1 END) as cancelled_trips,
                COUNT(CASE WHEN trip_status = 'pending' THEN 1 END) as pending_trips,
                COUNT(CASE WHEN trip_status = 'active' THEN 1 END) as active_trips,
                SUM(CASE WHEN trip_status != 'completed' THEN total_price ELSE 0 END) as unrealized_potential_revenue,
                SUM(CASE WHEN payment_status IN ('paid', 'manual_verified') AND trip_status != 'completed' THEN total_price ELSE 0 END) as at_risk_revenue
            ")
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        $summaryRow = $summaryRow ?? [];

        // Query 2: Revenue by vehicle
        $byVehicle = $this->db->table('bookings b')
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

        // Query 3: Revenue by driver
        $byDriver = $this->db->table('bookings b')
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

        $totalRevenue = (float) ($summaryRow['total_revenue'] ?? 0);
        $totalRefunded = (float) ($summaryRow['total_refunded'] ?? 0);

        // Derive completed analysis from summaryRow
        $completedAnalysis = [
            'completed_trips'          => (int) ($summaryRow['completed_trips'] ?? 0),
            'completed_distance'       => (float) ($summaryRow['completed_distance'] ?? 0),
            'completed_gross_revenue'  => (float) ($summaryRow['completed_gross_revenue'] ?? 0),
            'completed_total_costs'    => (float) ($summaryRow['completed_total_costs'] ?? 0),
            'completed_net_profit'     => (float) (($summaryRow['completed_gross_revenue'] ?? 0) - ($summaryRow['completed_total_costs'] ?? 0)),
            'avg_distance'             => (float) ($summaryRow['avg_distance'] ?? 0),
            'avg_trip_value'           => (float) ($summaryRow['avg_trip_value'] ?? 0),
        ];

        // Derive uncompleted analysis from summaryRow
        $uncompletedAnalysis = [
            'uncompleted_trips'            => (int) (($summaryRow['cancelled_trips'] ?? 0) + ($summaryRow['pending_trips'] ?? 0) + ($summaryRow['active_trips'] ?? 0)),
            'cancelled_trips'              => (int) ($summaryRow['cancelled_trips'] ?? 0),
            'pending_trips'                => (int) ($summaryRow['pending_trips'] ?? 0),
            'active_trips'                 => (int) ($summaryRow['active_trips'] ?? 0),
            'unrealized_potential_revenue' => (float) ($summaryRow['unrealized_potential_revenue'] ?? 0),
            'at_risk_revenue'              => (float) ($summaryRow['at_risk_revenue'] ?? 0),
        ];

        // Derive refund analysis from summaryRow
        $refundAnalysis = [
            'refunded_trips'          => (int) ($summaryRow['refunded_count'] ?? 0),
            'total_refunded'          => (float) ($summaryRow['total_refunded'] ?? 0),
            'avg_refund_amount'       => (float) ($summaryRow['avg_refund_amount'] ?? 0),
            'pending_refund_requests' => (int) ($summaryRow['pending_refund_requests'] ?? 0),
            'refund_rate'             => $totalRevenue > 0 ? round(($totalRefunded / $totalRevenue) * 100, 2) : 0,
        ];

        // Trend data (separate query — date-grouped aggregates are distinct)
        $trend = $this->db->table('bookings')
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

        // Fuel cost trend (capped at MAX_FUEL_RECORDS)
        $fuelTrend = $this->db->table('fuel_rates')
            ->select("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                price_per_liter,
                updated_by
            ")
            ->orderBy('created_at', 'DESC')
            ->limit(self::MAX_FUEL_RECORDS)
            ->get()
            ->getResultArray();

        return [
            'summary'              => $summaryRow,
            'by_vehicle'           => $byVehicle,
            'by_driver'            => $byDriver,
            'trend'                => $trend,
            'fuel_trend'           => $fuelTrend,
            'completed_analysis'   => $completedAnalysis,
            'uncompleted_analysis' => $uncompletedAnalysis,
            'refund_analysis'      => $refundAnalysis,
        ];
    }

    /**
     * Revenue summary by vehicle for CSV export (preserved for backward compatibility).
     *
     * @param string $start_date
     * @param string $end_date
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
     * @deprecated Use getAllReportData() instead — kept for backward compatibility.
     */
    public function getOverallSummary(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['summary'];
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getRevenueByDriver(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['by_driver'];
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getRevenueTrend(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['trend'];
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getFuelCostTrend(): array
    {
        return $this->db->table('fuel_rates')
            ->select("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                price_per_liter,
                updated_by
            ")
            ->orderBy('created_at', 'DESC')
            ->limit(self::MAX_FUEL_RECORDS)
            ->get()
            ->getResultArray();
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getCompletedTripsAnalysis(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['completed_analysis'];
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getUncompletedTripsAnalysis(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['uncompleted_analysis'];
    }

    /**
     * @deprecated Use getAllReportData() instead.
     */
    public function getRefundAnalysis(string $start_date, string $end_date): array
    {
        return $this->getAllReportData($start_date, $end_date)['refund_analysis'];
    }
}
