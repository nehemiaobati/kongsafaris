<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Entities\Vehicle;
use App\Modules\Trips\Entities\Driver;
use App\Modules\Trips\Libraries\TripQueryService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * FleetController
 *
 * Handles fleet management: vehicle and driver CRUD operations.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class FleetController extends BaseController
{
    private VehicleModel $vehicleModel;
    private DriverModel $driverModel;
    private TripQueryService $queryService;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
        $this->queryService = service('tripQueryService');
    }

    /**
     * Add a new vehicle to the fleet.
     */
    public function addVehicle(): ResponseInterface
    {
        $rules = [
            'plate_number'                => 'required|string|is_unique[vehicles.plate_number]',
            'model'                       => 'required|string',
            'fuel_efficiency'             => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km' => 'required|numeric|greater_than_equal_to[0]',
            'maintenance_reserve_per_km'  => 'required|numeric|greater_than_equal_to[0]',
            'fuel_type'                   => 'required|in_list[petrol,diesel]',
            'capacity'                    => 'required|integer|greater_than[0]',
            'status'                      => 'required|in_list[active,inactive,maintenance]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $vehicle = new Vehicle([
            'plate_number'                => (string) $this->request->getPost('plate_number'),
            'model'                       => (string) $this->request->getPost('model'),
            'fuel_efficiency'             => (float) $this->request->getPost('fuel_efficiency'),
            'target_profit_margin_per_km' => (float) $this->request->getPost('target_profit_margin_per_km'),
            'maintenance_reserve_per_km'  => (float) $this->request->getPost('maintenance_reserve_per_km'),
            'fuel_type'                   => (string) $this->request->getPost('fuel_type'),
            'capacity'                    => (int) $this->request->getPost('capacity'),
            'status'                      => (string) $this->request->getPost('status'),
        ]);

        $this->vehicleModel->insert($vehicle);

        return redirect()->back()->with('success', 'Vehicle added successfully.');
    }

    /**
     * Update vehicle information.
     */
    public function editVehicle(): ResponseInterface
    {
        $rules = [
            'vehicle_id'                   => 'required|integer',
            'plate_number'                 => 'required|string',
            'model'                        => 'required|string',
            'fuel_efficiency'              => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km'  => 'required|numeric|greater_than_equal_to[0]',
            'maintenance_reserve_per_km'   => 'required|numeric|greater_than_equal_to[0]',
            'fuel_type'                    => 'required|in_list[petrol,diesel]',
            'capacity'                     => 'required|integer|greater_than[0]',
            'status'                       => 'required|in_list[active,inactive,maintenance]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $vehicle_id = (int) $this->request->getPost('vehicle_id');
        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $this->vehicleModel->find($vehicle_id);

        if ($vehicle === null) {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }

        $vehicle->plate_number                = (string) $this->request->getPost('plate_number');
        $vehicle->model                       = (string) $this->request->getPost('model');
        $vehicle->fuel_efficiency             = (float) $this->request->getPost('fuel_efficiency');
        $vehicle->target_profit_margin_per_km = (float) $this->request->getPost('target_profit_margin_per_km');
        $vehicle->maintenance_reserve_per_km  = (float) $this->request->getPost('maintenance_reserve_per_km');
        $vehicle->fuel_type                   = (string) $this->request->getPost('fuel_type');
        $vehicle->capacity                    = (int) $this->request->getPost('capacity');
        $vehicle->status                      = (string) $this->request->getPost('status');

        $this->vehicleModel->update($vehicle_id, $vehicle);

        return redirect()->back()->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove a vehicle from the fleet.
     */
    public function deleteVehicle(int $id): ResponseInterface
    {
        /** @var \App\Modules\Trips\Entities\Vehicle|null $vehicle */
        $vehicle = $this->vehicleModel->find($id);

        if ($vehicle === null) {
            return redirect()->back()->with('error', 'Vehicle not found.');
        }

        $this->vehicleModel->delete($id);

        return redirect()->back()->with('success', 'Vehicle deleted successfully.');
    }

    /**
     * Register a new driver in the system.
     */
    public function addDriver(): ResponseInterface
    {
        $rules = [
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'email'               => 'required|valid_email|is_unique[users.email]',
            'password'            => 'required|min_length[6]',
            'license_number'      => 'required|string|is_unique[drivers.license_number]',
            'allowance_flat_rate' => 'required|numeric|greater_than_equal_to[0]',
            'status'              => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $userModel = new \App\Modules\Auth\Models\UserModel();
            $user = new \App\Modules\Auth\Entities\User([
                'first_name' => (string) $this->request->getPost('first_name'),
                'last_name'  => (string) $this->request->getPost('last_name'),
                'email'      => (string) $this->request->getPost('email'),
                'role'       => 'driver',
            ]);
            $user->setPassword((string) $this->request->getPost('password'));
            $userId = $userModel->insert($user);

            if ($userId === false) {
                throw new \RuntimeException('Failed to create user record for driver.');
            }

            $driver = new Driver([
                'user_id'             => (int) $userId,
                'license_number'      => (string) $this->request->getPost('license_number'),
                'allowance_flat_rate' => (float) $this->request->getPost('allowance_flat_rate'),
                'status'              => (string) $this->request->getPost('status'),
            ]);

            $this->driverModel->insert($driver);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Driver registration failed', [
                'exception' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to register driver. ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Driver registered successfully.');
    }

    /**
     * Update driver information.
     */
    public function editDriver(): ResponseInterface
    {
        $rules = [
            'driver_id'          => 'required|integer',
            'license_number'     => 'required|string',
            'allowance_flat_rate' => 'required|numeric|greater_than_equal_to[0]',
            'status'             => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $driver_id = (int) $this->request->getPost('driver_id');
        $driver = $this->driverModel->find($driver_id);

        if ($driver === null) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        $driver->license_number = (string) $this->request->getPost('license_number');
        $driver->allowance_flat_rate = (float) $this->request->getPost('allowance_flat_rate');
        $driver->status = (string) $this->request->getPost('status');

        $this->driverModel->update($driver_id, $driver);

        return redirect()->back()->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove a driver from the system.
     */
    public function deleteDriver(int $id): ResponseInterface
    {
        $driver = $this->driverModel->find($id);

        if ($driver === null) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        $this->driverModel->delete($id);

        return redirect()->back()->with('success', 'Driver deleted successfully.');
    }
}
