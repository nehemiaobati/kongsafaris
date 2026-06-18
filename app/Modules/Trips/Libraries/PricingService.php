<?php

declare(strict_types=1);

namespace App\Modules\Trips\Libraries;

use App\Modules\Trips\Entities\Vehicle;
use App\Modules\Trips\Entities\Driver;
use App\Modules\Trips\Models\FuelRateModel;

class PricingService
{
    private float $base_booking_fee = 50.00; // Configurable base booking fee

    /**
     * Calculate quotation pricing details based on vehicle, driver, and distance.
     *
     * @param Vehicle $vehicle
     * @param Driver  $driver
     * @param float   $distance_km
     *
     * @return array{
     *     base_booking_fee: float,
     *     per_km_fuel_cost: float,
     *     maintenance_reserve: float,
     *     driver_allowance: float,
     *     total_price: float
     * }
     */
    public function calculateQuote(Vehicle $vehicle, Driver $driver, float $distance_km): array
    {
        // 1. Fetch current fuel rate matching the vehicle's fuel type
        $fuelRateModel = new FuelRateModel();
        $vehicleFuelType = $vehicle->fuel_type ?? 'petrol';
        /** @var \App\Modules\Trips\Entities\FuelRate|null $fuelRate */
        $fuelRate = $fuelRateModel->where('fuel_type', $vehicleFuelType)->orderBy('created_at', 'DESC')->first();

        $price_per_liter = $fuelRate !== null ? (float)$fuelRate->price_per_liter : 1.50; // Fallback if no rate exists

        // 2. Calculate fuel cost per Km
        // fuel_efficiency is in Km per Liter. Fuel cost per Km = price_per_liter / fuel_efficiency.
        $fuel_efficiency = $vehicle->fuel_efficiency > 0 ? (float)$vehicle->fuel_efficiency : 8.0;
        $fuel_cost_per_km = $price_per_liter / $fuel_efficiency;

        // Per-km fuel cost overall
        $total_fuel_cost = $distance_km * $fuel_cost_per_km;

        // 3. Maintenance Reserve: Distance * maintenance_reserve_per_km
        $maintenance_reserve_per_km = (float)$vehicle->maintenance_reserve_per_km;
        $total_maintenance_reserve = $distance_km * $maintenance_reserve_per_km;

        // 4. Vehicle rate per Km (Fuel cost per Km + Target profit margin per Km)
        $target_profit_margin_per_km = (float)$vehicle->target_profit_margin_per_km;
        $vehicle_rate_per_km = $fuel_cost_per_km + $target_profit_margin_per_km;

        // 5. Driver allowance
        $driver_allowance = (float)$driver->allowance_flat_rate;

        // 6. Formula calculation: Base Booking Fee + (Distance * Vehicle Rate per Km) + Driver Allowance + Maintenance Reserve
        // Which expands to: Base Booking Fee + (Distance * (Fuel cost per Km + Profit Margin)) + Driver Allowance + (Distance * Maintenance Reserve)
        $distance_rate_portion = $distance_km * $vehicle_rate_per_km;

        $total_price = $this->base_booking_fee + $distance_rate_portion + $driver_allowance + $total_maintenance_reserve;

        return [
            'base_booking_fee'    => $this->base_booking_fee,
            'per_km_fuel_cost'    => round($total_fuel_cost, 2),
            'maintenance_reserve' => round($total_maintenance_reserve, 2),
            'driver_allowance'    => round($driver_allowance, 2),
            'total_price'         => round($total_price, 2),
        ];
    }
}
