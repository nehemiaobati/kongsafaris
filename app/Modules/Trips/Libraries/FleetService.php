<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Trips\Models\DriverModel;

/**
 * FleetService
 *
 * Encapsulates all fleet management operations: vehicle and driver
 * CRUD operations. Controllers MUST delegate all fleet business logic
 * to this service.
 *
 * @package App\Modules\Trips\Libraries
 * @author Senior Developer
 * @since 1.0.0
 */
class FleetService
{
    private VehicleModel $vehicleModel;
    private DriverModel $driverModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
    }

    /**
     * Add a new vehicle to the fleet.
     *
     * @param array $data Vehicle fields
     *
     * @return array{status: bool, message: string}
     */
    public function addVehicle(array $data): array
    {
        $this->vehicleModel->insert($data);
        return ['status' => true, 'message' => 'Vehicle added successfully.'];
    }

    /**
     * Edit an existing vehicle.
     *
     * @param array $data Vehicle fields including id
     *
     * @return array{status: bool, message: string}
     */
    public function editVehicle(array $data): array
    {
        $vehicleId = (int) ($data['id'] ?? 0);
        if ($vehicleId === 0) {
            return ['status' => false, 'message' => 'Vehicle ID is required.'];
        }

        $this->vehicleModel->update($vehicleId, $data);
        return ['status' => true, 'message' => 'Vehicle updated successfully.'];
    }

    /**
     * Delete a vehicle from the fleet.
     *
     * @param int $vehicleId
     *
     * @return array{status: bool, message: string}
     */
    public function deleteVehicle(int $vehicleId): array
    {
        $this->vehicleModel->delete($vehicleId);
        return ['status' => true, 'message' => 'Vehicle deleted successfully.'];
    }

    /**
     * Add a new driver.
     *
     * @param array $data Driver fields
     *
     * @return array{status: bool, message: string}
     */
    public function addDriver(array $data): array
    {
        $this->driverModel->insert($data);
        return ['status' => true, 'message' => 'Driver added successfully.'];
    }

    /**
     * Edit an existing driver.
     *
     * @param array $data Driver fields including id
     *
     * @return array{status: bool, message: string}
     */
    public function editDriver(array $data): array
    {
        $driverId = (int) ($data['id'] ?? 0);
        if ($driverId === 0) {
            return ['status' => false, 'message' => 'Driver ID is required.'];
        }

        $this->driverModel->update($driverId, $data);
        return ['status' => true, 'message' => 'Driver updated successfully.'];
    }

    /**
     * Delete a driver.
     *
     * @param int $driverId
     *
     * @return array{status: bool, message: string}
     */
    public function deleteDriver(int $driverId): array
    {
        $this->driverModel->delete($driverId);
        return ['status' => true, 'message' => 'Driver deleted successfully.'];
    }
}