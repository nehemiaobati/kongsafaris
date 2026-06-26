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
 * Uses the consolidated ReportService (3 queries instead of 8).
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class ReportController extends BaseController
{
    /**
     * Display reporting dashboard.
     */
    public function index(): string|ResponseInterface
    {
        if (! session()->get('isLoggedIn') || ! in_array(session()->get('role'), ['manager', 'admin'], true)) {
            return redirect()->to(url_to('auth.login'));
        }

        $startDate = (string) $this->request->getGet('start_date');
        $endDate   = (string) $this->request->getGet('end_date');

        if (empty($startDate)) {
            $startDate = Time::now()->subDays(30)->toDateString();
        }
        if (empty($endDate)) {
            $endDate = Time::now()->toDateString();
        }

        $reportService = new ReportService();
        $reportData = $reportService->getAllReportData($startDate, $endDate);

        return view('App\Modules\Trips\Views\reports', [
            'pageTitle'             => 'Reports & Analytics | Kong Safaris',
            'metaDescription'       => 'View revenue reports, vehicle profitability, and fuel cost trends.',
            'canonicalUrl'          => url_to('trips.reports'),
            'robotsTag'             => 'noindex, nofollow',
            'start_date'            => $startDate,
            'end_date'              => $endDate,
            'summary'               => $reportData['summary'],
            'by_vehicle'            => $reportData['by_vehicle'],
            'by_driver'             => $reportData['by_driver'],
            'trend'                 => $reportData['trend'],
            'fuel_trend'            => $reportData['fuel_trend'],
            'completed_analysis'    => $reportData['completed_analysis'],
            'uncompleted_analysis'  => $reportData['uncompleted_analysis'],
            'refund_analysis'       => $reportData['refund_analysis'],
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

        $startDate = (string) $this->request->getGet('start_date');
        $endDate   = (string) $this->request->getGet('end_date');

        if (empty($startDate)) {
            $startDate = Time::now()->subDays(30)->toDateString();
        }
        if (empty($endDate)) {
            $endDate = Time::now()->toDateString();
        }

        $reportService = new ReportService();
        $rows = $reportService->getRevenueByVehicle($startDate, $endDate);

        $csv = "Vehicle Model,Plate Number,Trips,Distance (Km),Total Revenue,Fuel Cost,Maintenance,Allowances,Net Profit (after costs)\n";

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

        $filename = 'kong-safaris-report-' . $startDate . '-to-' . $endDate . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}
