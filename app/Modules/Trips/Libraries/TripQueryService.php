<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\BookingModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\FuelRateModel;
use App\Modules\Trips\Models\SystemSettingModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Entities\SystemSetting;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\ConnectionInterface;

/**
 * TripQueryService
 *
 * Encapsulates all read queries for the Trips module.
 * Controllers MUST delegate all data retrieval to this service
 * to satisfy Rule 4.1 (no direct database queries in controllers).
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class TripQueryService
{
    private ConnectionInterface $db;
    private BookingModel $bookingModel;
    private DriverModel $driverModel;
    private VehicleModel $vehicleModel;
    private FuelRateModel $fuelRateModel;
    private SystemSettingModel $settingModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->bookingModel = new BookingModel();
        $this->driverModel = new DriverModel();
        $this->vehicleModel = new VehicleModel();
        $this->fuelRateModel = new FuelRateModel();
        $this->settingModel = new SystemSettingModel();
    }

    /**
     * Retrieve paginated bookings with related entity data.
     *
     * @param int $perPage
     *
     * @return array bookings and pager
     */
    public function getDashboardBookings(int $perPage = 10): array
    {
        $bookings = $this->bookingModel->select('bookings.*, vehicles.plate_number, vehicles.model, users.first_name, users.last_name')
            ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
            ->join('drivers', 'drivers.id = bookings.driver_id')
            ->join('users', 'users.id = drivers.user_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->paginate($perPage, 'default');

        return [
            'bookings' => $bookings,
            'pager'    => $this->bookingModel->pager,
        ];
    }

    /**
     * Retrieve the latest fuel rates for petrol and diesel.
     *
     * @return array petrol and diesel rates
     */
    public function getCurrentFuelRates(): array
    {
        $petrolRate = $this->fuelRateModel->where('fuel_type', 'petrol')->orderBy('created_at', 'DESC')->first();
        $dieselRate = $this->fuelRateModel->where('fuel_type', 'diesel')->orderBy('created_at', 'DESC')->first();

        return [
            'petrol' => $petrolRate !== null ? (float) $petrolRate->price_per_liter : 188.50,
            'diesel' => $dieselRate !== null ? (float) $dieselRate->price_per_liter : 175.50,
        ];
    }

    /**
     * Retrieve all active vehicles.
     *
     * @return array active vehicles
     */
    public function getActiveVehicles(): array
    {
        return $this->vehicleModel->findAll();
    }

    /**
     * Retrieve all drivers with associated user data.
     *
     * @return array drivers list
     */
    public function getDriversList(): array
    {
        return $this->db->table('drivers')
            ->select('drivers.id, drivers.license_number, drivers.allowance_flat_rate, drivers.status, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = drivers.user_id')
            ->get()
            ->getResultArray();
    }

    /**
     * Retrieve bookings awaiting refund.
     *
     * @return array refund requests
     */
    public function getRefundRequests(): array
    {
        return $this->bookingModel->select('bookings.*, users.first_name, users.last_name')
            ->join('users', 'users.id = bookings.customer_id')
            ->where('payment_status', 'refund_requested')
            ->findAll();
    }

    /**
     * Retrieve a single system setting value.
     *
     * @param string $key
     *
     * @return \App\Modules\Trips\Entities\SystemSetting|null setting entity or null
     */
    public function getSystemSetting(string $key): ?SystemSetting
    {
        return $this->settingModel->where('setting_key', $key)->first();
    }

    /**
     * Retrieve customer list for dropdowns.
     *
     * @return array customers list
     */
    public function getCustomersList(): array
    {
        return $this->db->table('users')
            ->select('id, first_name, last_name, email')
            ->where('role', 'customer')
            ->orderBy('first_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Retrieve drivers for assignment dropdowns.
     *
     * @return array drivers list
     */
    public function getActiveDriversList(): array
    {
        return $this->db->table('drivers')
            ->select('drivers.id, drivers.status, users.first_name, users.last_name')
            ->join('users', 'users.id = drivers.user_id')
            ->where('drivers.status', 'available')
            ->orderBy('users.first_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Retrieve all active vehicles for selection.
     *
     * @return array vehicles list
     */
    public function getVehiclesList(): array
    {
        return $this->vehicleModel->where('status', 'active')->findAll();
    }
}
