<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Libraries\ReportService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * ReportController
 *
 * Exports reporting dashboards and CSV data for manager/admin roles.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class ReportController extends BaseController
{
    /**
     * Display reporting dashboard with Chart.js visualizations.
     */
    public function index(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $start_date = (string) $this->request->getGet('start_date');
        $end_date   = (string) $this->request->getGet('end_date');

        // Default to last 30 days
        if (empty($start_date)) {
            $start_date = Time::now()->subDays(30)->toDateString();
        }
        if (empty($end_date)) {
            $end_date = Time::now()->toDateString();
        }

        $reportService = new ReportService();

        $summary       = $reportService->getOverallSummary($start_date, $end_date);
        $byVehicle     = $reportService->getRevenueByVehicle($start_date, $end_date);
        $byDriver      = $reportService->getRevenueByDriver($start_date, $end_date);
        $trend         = $reportService->getRevenueTrend($start_date, $end_date);
        $fuelTrend     = $reportService->getFuelCostTrend();

        return view('App\Modules\Trips\Views\reports', [
            'pageTitle'       => 'Reports & Analytics | Kong Safaris',
            'metaDescription' => 'View revenue reports, vehicle profitability, and fuel cost trends.',
            'canonicalUrl'    => url_to('trips.reports'),
            'robotsTag'       => 'noindex, nofollow',
            'start_date'      => $start_date,
            'end_date'        => $end_date,
            'summary'         => $summary,
            'by_vehicle'      => $byVehicle,
            'by_driver'       => $byDriver,
            'trend'           => $trend,
            'fuel_trend'      => $fuelTrend,
        ]);
    }

    /**
     * Export revenue by vehicle to CSV.
     */
    public function exportCsv(): ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $start_date = (string) $this->request->getGet('start_date');
        $end_date   = (string) $this->request->getGet('end_date');

        if (empty($start_date)) {
            $start_date = Time::now()->subDays(30)->toDateString();
        }
        if (empty($end_date)) {
            $end_date = Time::now()->toDateString();
        }

        $reportService = new ReportService();
        $rows = $reportService->getRevenueByVehicle($start_date, $end_date);

        // Build CSV
        $csv = "Vehicle Model,Plate Number,Trips,Distance (Km),Gross Revenue,Fuel Cost,Maintenance,Allowances,Net Profit\n";

        foreach ($rows as $row) {
            $net = (float) $row['gross_revenue']
                 - (float) $row['total_fuel']
                 - (float) $row['total_maintenance']
                 - (float) $row['total_allowances'];

            $csv .= '"' . $row['model'] . '","' . $row['plate_number'] . '",'
                  . $row['trip_count'] . ',' . round((float) $row['total_km'], 2) . ','
                  . round((float) $row['gross_revenue'], 2) . ','
                  . round((float) $row['total_fuel'], 2) . ','
                  . round((float) $row['total_maintenance'], 2) . ','
                  . round((float) $row['total_allowances'], 2) . ','
                  . round($net, 2) . "\n";
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="kong-safaris-report-' . $start_date . '-to-' . $end_date . '.csv"')
            ->setBody($csv);
    }
}