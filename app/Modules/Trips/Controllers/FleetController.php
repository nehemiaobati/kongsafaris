<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\DriverModel;
use App\Modules\Trips\Models\VehicleModel;
use App\Modules\Auth\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * FleetController
 *
 * Manages vehicle and driver CRUD operations for fleet administration.
 * Access restricted to manager and admin roles.
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class FleetController extends BaseController
{
    /**
     * Add a new vehicle to the fleet.
     */
    public function addVehicle(): ResponseInterface
    {
        $rules = [
            'plate_number'                => 'required|is_unique[vehicles.plate_number]',
            'model'                       => 'required|string',
            'fuel_efficiency'             => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km' => 'required|numeric',
            'maintenance_reserve_per_km'  => 'required|numeric',
            'status'                      => 'required|in_list[active,maintenance,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vehicleModel = new VehicleModel();
        $vehicle = new \App\Modules\Trips\Entities\Vehicle($this->request->getPost());
        $vehicleModel->insert($vehicle);

        return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle added successfully.');
    }

    /**
     * Edit an existing vehicle.
     */
    public function editVehicle(): ResponseInterface
    {
        $id = (int) $this->request->getPost('vehicle_id');
        $rules = [
            'plate_number'                => "required|is_unique[vehicles.plate_number,id,{$id}]",
            'model'                       => 'required|string',
            'fuel_efficiency'             => 'required|numeric|greater_than[0]',
            'target_profit_margin_per_km' => 'required|numeric',
            'maintenance_reserve_per_km'  => 'required|numeric',
            'status'                      => 'required|in_list[active,maintenance,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($id);
        if ($vehicle !== null) {
            $vehicle->fill($this->request->getPost());
            $vehicleModel->update($id, $vehicle);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Delete a vehicle from the fleet.
     */
    public function deleteVehicle(int $id): ResponseInterface
    {
        $vehicleModel = new VehicleModel();
        try {
            $vehicleModel->delete($id);
            return redirect()->to(url_to('trips.manager'))->with('success', 'Vehicle deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Cannot delete vehicle. It may be referenced in bookings.');
        }
    }

    /**
     * Register a new driver (creates user + driver records).
     */
    public function addDriver(): ResponseInterface
    {
        $rules = [
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'email'               => 'required|valid_email|is_unique[users.email]',
            'password'            => 'required|min_length[6]',
            'license_number'      => 'required|is_unique[drivers.license_number]',
            'allowance_flat_rate' => 'required|numeric',
            'status'              => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $user = new \App\Modules\Auth\Entities\User([
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'role'       => 'driver',
            ]);
            $user->setPassword((string) $this->request->getPost('password'));

            $userModel = new UserModel();
            $userModel->insert($user);
            $userId = $db->insertID();

            $driver = new \App\Modules\Trips\Entities\Driver([
                'user_id'             => $userId,
                'license_number'      => $this->request->getPost('license_number'),
                'allowance_flat_rate' => $this->request->getPost('allowance_flat_rate'),
                'status'              => $this->request->getPost('status'),
            ]);

            $driverModel = new DriverModel();
            $driverModel->insert($driver);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to save driver transactional records.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Driver registered successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Add Driver Failure', ['exception' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'An error occurred while registering the driver.');
        }
    }

    /**
     * Edit an existing driver record.
     */
    public function editDriver(): ResponseInterface
    {
        $id = (int) $this->request->getPost('driver_id');
        $rules = [
            'license_number'      => "required|is_unique[drivers.license_number,id,{$id}]",
            'allowance_flat_rate' => 'required|numeric',
            'status'              => 'required|in_list[available,on_trip,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $driverModel = new DriverModel();
        /** @var \App\Modules\Trips\Entities\Driver|null $driver */
        $driver = $driverModel->find($id);
        if ($driver !== null) {
            $driver->fill($this->request->getPost());
            $driverModel->update($id, $driver);
        }

        return redirect()->to(url_to('trips.manager'))->with('success', 'Driver details updated.');
    }

    /**
     * Delete a driver and associated user account.
     */
    public function deleteDriver(int $id): ResponseInterface
    {
        $driverModel = new DriverModel();
        $db = \Config\Database::connect();

        $driver = $driverModel->find($id);
        if ($driver === null) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        $db->transStart();

        try {
            $userId = $driver->user_id;
            $driverModel->delete($driver->id);

            $userModel = new UserModel();
            $userModel->delete($userId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Failed to delete driver record transaction.');
            }

            return redirect()->to(url_to('trips.manager'))->with('success', 'Driver record deleted.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Cannot delete driver. Active references exist.');
        }
    }
}