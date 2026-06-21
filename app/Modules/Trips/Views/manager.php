<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        color: #6c757d !important;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        background: transparent;
    }

    .nav-tabs .nav-link:hover {
        border-color: rgba(13, 110, 253, 0.15) rgba(13, 110, 253, 0.15) transparent;
        color: #212529 !important;
    }

    .nav-tabs .nav-link.active {
        background-color: rgba(13, 110, 253, 0.05) !important;
        border-color: rgba(13, 110, 253, 0.3) rgba(13, 110, 253, 0.3) transparent !important;
        color: var(--safari-accent) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03) !important;
        transition: background-color 0.2s ease-in-out;
    }

    @keyframes pulse-opacity {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    .animate-pulse {
        animation: pulse-opacity 1.5s infinite ease-in-out;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/** @var float $base_booking_fee */
/** @var float $currentPetrolRate */
/** @var float $currentDieselRate */
/** @var array<int, object> $bookings */
/** @var array<int, object> $refundRequests */
/** @var array<int, object> $vehicles */
/** @var array<int, array<string, mixed>> $drivers */
/** @var string $googleApiKey */
?>
<!-- Top Info Sections -->
<div class="row mb-4">
    <!-- System Settings Card -->
    <div class="col-md-6 mb-3">
        <div class="card blueprint-card p-4 h-100">
            <h5 class="fw-bold text-accent mb-3">System Settings</h5>
            <p class="text-muted small mb-3">Configure global pricing parameters.</p>

            <form action="<?= url_to('trips.manager.update_settings') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <input type="number" step="0.01" min="0" class="form-control" id="baseFeeInput" name="base_booking_fee" value="<?= esc($base_booking_fee) ?>" required>
                    <label for="baseFeeInput">Base Booking Fee (USD)</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Base Fee</button>
            </form>
        </div>
    </div>

    <!-- Fuel Rate Administration Card -->
    <div class="col-md-6 mb-3">
        <div class="card blueprint-card p-4 h-100">
            <h5 class="fw-bold text-accent mb-3">Fuel Price Admin</h5>
            <p class="text-muted small mb-3">Set the global price per liter for each fuel type used by the Dynamic Pricing Engine.</p>

            <div class="row g-2">
                <div class="col-12">
                    <form action="<?= url_to('trips.fuel.update') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="fuel_type" value="petrol">
                        <div class="form-floating mb-2">
                            <input type="number" step="0.01" min="0.01" class="form-control" id="petrolFuelInput" name="price_per_liter" value="<?= (string)$currentPetrolRate ?>" required>
                            <label for="petrolFuelInput">Petrol ($/L)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Petrol</button>
                    </form>
                </div>
                <div class="col-12">
                    <form action="<?= url_to('trips.fuel.update') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="fuel_type" value="diesel">
                        <div class="form-floating mb-2">
                            <input type="number" step="0.01" min="0.01" class="form-control" id="dieselFuelInput" name="price_per_liter" value="<?= (string)$currentDieselRate ?>" required>
                            <label for="dieselFuelInput">Diesel ($/L)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Diesel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Fleet Status Summary -->
    <div class="col-md-12 mb-3">
        <div class="card blueprint-card p-4 h-100">
            <h5 class="fw-bold text-accent mb-3">Fleet Operations Overview</h5>
            <div class="row g-3">
                <div class="col-sm-3">
                    <div class="p-3 border rounded text-center">
                        <span class="text-muted small">Active Trips</span>
                        <h2 class="fw-bold mt-1 text-success"><?= esc(count(array_filter($bookings, fn($b) => $b->trip_status === 'active'))) ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="p-3 border rounded text-center">
                        <span class="text-muted small">Refund Requests</span>
                        <h2 class="fw-bold mt-1 text-danger"><?= (string)count($refundRequests) ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="p-3 border rounded text-center">
                        <span class="text-muted small">Fleet Vehicles</span>
                        <h2 class="fw-bold mt-1 text-info"><?= (string)count($vehicles) ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="p-3 border rounded text-center">
                        <span class="text-muted small">Petrol Rate</span>
                        <h2 class="fw-bold mt-1 text-accent">$<?= number_format((float)$currentPetrolRate, 2) ?></h2>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="p-3 border rounded text-center">
                        <span class="text-muted small">Diesel Rate</span>
                        <h2 class="fw-bold mt-1 text-warning">$<?= number_format((float)$currentDieselRate, 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Control -->
<div class="card blueprint-card p-4 mb-4">
    <ul class="nav nav-tabs mb-4" id="managerTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings-panel" type="button" role="tab">Bookings Log</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold position-relative" id="refunds-tab" data-bs-toggle="tab" data-bs-target="#refunds-panel" type="button" role="tab">
                Refund Requests
                <?php if (!empty($refundRequests)): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= count($refundRequests) ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehicles-panel" type="button" role="tab">Manage Vehicles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers-panel" type="button" role="tab">Manage Drivers</button>
        </li>
        <?php if (session()->get('role') === 'admin'): ?>
            <li class="nav-item" role="presentation">
                <a href="<?= url_to('auth.admin.users') ?>" class="nav-link fw-bold">User Management</a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="managerTabContent">
        <!-- 1. Bookings Tab -->
        <div class="tab-pane fade show active" id="bookings-panel" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Driver / Vehicle</th>
                            <th>Route Addresses</th>
                            <th>Distance / Cost</th>
                            <th>Payment</th>
                            <th>Trip Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No bookings found in the system log.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><strong>#<?= esc($booking->id) ?></strong></td>
                                    <td>
                                        <div class="small">
                                            <strong><?= esc($booking->first_name . ' ' . $booking->last_name) ?></strong>
                                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 edit-booking-driver-btn" data-booking-id="<?= $booking->id ?>" data-driver-id="<?= $booking->driver_id ?>" title="Change Driver">Edit</button>
                                            <br>
                                            <span class="text-muted"><?= esc($booking->model) ?> (<?= esc($booking->plate_number) ?>)</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-truncate" style="max-width: 260px;">
                                            <strong>From:</strong> <?= esc($booking->pickup_address) ?><br>
                                            <strong>To:</strong> <?= esc($booking->dropoff_address) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary mb-1"><?= esc($booking->distance_km) ?> Km</span><br>
                                        <strong class="text-accent">$<?= number_format($booking->total_price, 2) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($booking->payment_status === 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($booking->payment_status === 'refund_requested'): ?>
                                            <span class="badge bg-info">Refund</span>
                                        <?php elseif ($booking->payment_status === 'refunded'): ?>
                                            <span class="badge bg-secondary">Refunded</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><?= esc(ucfirst($booking->payment_status)) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($booking->trip_status === 'active'): ?>
                                            <span class="badge bg-success animate-pulse">Active</span>
                                        <?php elseif ($booking->trip_status === 'completed'): ?>
                                            <span class="badge bg-info">Completed</span>
                                        <?php elseif ($booking->trip_status === 'cancelled'): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                                            <?php if ($booking->trip_status === 'pending'): ?>
                                                <button class="btn btn-outline-primary btn-sm edit-booking-btn"
                                                    data-id="<?= $booking->id ?>"
                                                    data-pickup="<?= esc($booking->pickup_address) ?>"
                                                    data-dropoff="<?= esc($booking->dropoff_address) ?>"
                                                    data-pickup-lat="<?= $booking->pickup_latitude ?>"
                                                    data-pickup-lng="<?= $booking->pickup_longitude ?>"
                                                    data-dropoff-lat="<?= $booking->dropoff_latitude ?>"
                                                    data-dropoff-lng="<?= $booking->dropoff_longitude ?>"
                                                    data-vehicle="<?= $booking->vehicle_id ?>"
                                                    data-driver="<?= $booking->driver_id ?>"
                                                    data-distance="<?= $booking->distance_km ?>"
                                                    data-price="<?= $booking->total_price ?>"
                                                    data-payment="<?= $booking->payment_status ?>"
                                                    data-trip="<?= $booking->trip_status ?>">Edit</button>
                                            <?php endif; ?>
                                            <?php if ($booking->trip_status === 'pending'): ?>
                                                <form action="<?= url_to('trips.manager.cancel') ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel booking #<?= $booking->id ?>?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="booking_id" value="<?= $booking->id ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($booking->trip_status !== 'cancelled' && in_array($booking->payment_status, ['pending', 'failed'], true)): ?>
                                                <button class="btn btn-success btn-sm collect-payment-btn"
                                                    data-id="<?= $booking->id ?>"
                                                    data-total="<?= number_format($booking->total_price, 2) ?>"
                                                    data-customer="<?= esc($booking->first_name . ' ' . $booking->last_name) ?>">
                                                    Collect Payment
                                                </button>
                                            <?php endif; ?>

                                            <?php if ($booking->trip_status === 'active'): ?>
                                                <button class="btn btn-primary btn-sm px-3 track-btn" data-id="<?= $booking->id ?>" data-pickup-lat="<?= $booking->pickup_latitude ?>" data-pickup-lng="<?= $booking->pickup_longitude ?>" data-dropoff-lat="<?= $booking->dropoff_latitude ?>" data-dropoff-lng="<?= $booking->dropoff_longitude ?>">Live Track</button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>Offline</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links('default', 'bootstrap5') ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Refunds Tab -->
        <div class="tab-pane fade" id="refunds-panel" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Refund Amount</th>
                            <th>Paystack Reference</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($refundRequests)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No active refund requests requiring clearance.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($refundRequests as $refund): ?>
                                <tr>
                                    <td><strong>#<?= $refund->id ?></strong></td>
                                    <td><?= esc($refund->first_name . ' ' . $refund->last_name) ?></td>
                                    <td class="text-accent fw-bold">$<?= number_format($refund->total_price, 2) ?></td>
                                    <td><code><?= esc($refund->paystack_reference ?? 'Manual-Charge') ?></code></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <?php if (!empty($refund->paystack_reference)): ?>
                                                <form action="<?= url_to('trips.manager.refund') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="booking_id" value="<?= $refund->id ?>">
                                                    <input type="hidden" name="action" value="refund_paystack">
                                                    <button type="submit" class="btn btn-danger btn-sm">Refund via Paystack</button>
                                                </form>
                                            <?php endif; ?>
                                            <form action="<?= url_to('trips.manager.refund') ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="booking_id" value="<?= $refund->id ?>">
                                                <input type="hidden" name="action" value="refund_manual">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Clear Manually</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Vehicles Tab -->
        <div class="tab-pane fade" id="vehicles-panel" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-accent mb-0">Registered Vehicles</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVehicleModal">+ Add Vehicle</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Plate</th>
                            <th>Model</th>
                            <th>Fuel Type</th>
                            <th>Fuel efficiency</th>
                            <th>Margin/Km</th>
                            <th>Reserve/Km</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><strong><?= esc($v->plate_number) ?></strong></td>
                                <td><?= esc($v->model) ?></td>
                                <td><span class="badge bg-<?= $v->fuel_type === 'diesel' ? 'warning text-dark' : 'secondary' ?>"><?= esc(ucfirst($v->fuel_type)) ?></span></td>
                                <td><?= esc($v->fuel_efficiency) ?> Km/L</td>
                                <td>$<?= esc($v->target_profit_margin_per_km) ?></td>
                                <td>$<?= esc($v->maintenance_reserve_per_km) ?></td>
                                <td>
                                    <span class="badge bg-<?= $v->status === 'active' ? 'success' : ($v->status === 'maintenance' ? 'warning text-dark' : 'danger') ?>">
                                        <?= esc(ucfirst($v->status)) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm edit-vehicle-btn"
                                            data-id="<?= $v->id ?>"
                                            data-plate="<?= esc($v->plate_number) ?>"
                                            data-model="<?= esc($v->model) ?>"
                                            data-fuel="<?= $v->fuel_efficiency ?>"
                                            data-margin="<?= $v->target_profit_margin_per_km ?>"
                                            data-reserve="<?= $v->maintenance_reserve_per_km ?>"
                                            data-fuel-type="<?= $v->fuel_type ?>"
                                            data-status="<?= esc($v->status) ?>">Edit</button>
                                        <form action="<?= base_url('trips/vehicles/delete/' . $v->id) ?>" method="POST" onsubmit="return confirm('Delete vehicle?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Drivers Tab -->
        <div class="tab-pane fade" id="drivers-panel" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-accent mb-0">Registered Drivers</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDriverModal">+ Add Driver</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Driver Name</th>
                            <th>Email</th>
                            <th>License</th>
                            <th>Flat Rate Allowance</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drivers as $d): ?>
                            <tr>
                                <td><strong><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></strong></td>
                                <td><?= esc($d['email']) ?></td>
                                <td><code><?= esc($d['license_number']) ?></code></td>
                                <td>$<?= number_format((float)$d['allowance_flat_rate'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $d['status'] === 'available' ? 'success' : ($d['status'] === 'on_trip' ? 'info' : 'secondary') ?>">
                                        <?= esc(ucfirst($d['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm edit-driver-btn"
                                            data-id="<?= $d['id'] ?>"
                                            data-license="<?= esc($d['license_number']) ?>"
                                            data-allowance="<?= $d['allowance_flat_rate'] ?>"
                                            data-status="<?= esc($d['status']) ?>">Edit</button>
                                        <form action="<?= base_url('trips/drivers/delete/' . $d['id']) ?>" method="POST" onsubmit="return confirm('Delete driver?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Add Fleet Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.vehicle.add') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="plate_number" placeholder="KAA 123A" required>
                        <label>Plate Number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="model" placeholder="Toyota Safari" required>
                        <label>Vehicle Model</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.1" class="form-control" name="fuel_efficiency" placeholder="8.0" required>
                        <label>Fuel Efficiency (Km per Liter)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="target_profit_margin_per_km" placeholder="1.50" required>
                        <label>Target Profit Margin per Km (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="maintenance_reserve_per_km" placeholder="0.50" required>
                        <label>Maintenance Reserve per Km (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="fuel_type">
                            <option value="petrol" selected>Petrol</option>
                            <option value="diesel">Diesel</option>
                        </select>
                        <label>Fuel Type</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="status">
                            <option value="active" selected>Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Edit Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.vehicle.edit') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="vehicle_id" id="edit_v_id">
                <div class="modal-body p-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="plate_number" id="edit_v_plate" required>
                        <label>Plate Number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="model" id="edit_v_model" required>
                        <label>Vehicle Model</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.1" class="form-control" name="fuel_efficiency" id="edit_v_fuel" required>
                        <label>Fuel Efficiency (Km/L)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="target_profit_margin_per_km" id="edit_v_margin" required>
                        <label>Target Profit Margin / Km (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="maintenance_reserve_per_km" id="edit_v_reserve" required>
                        <label>Maintenance Reserve / Km (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="fuel_type" id="edit_v_fuel_type">
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                        </select>
                        <label>Fuel Type</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="status" id="edit_v_status">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Register Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.driver.add') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                        <label>First Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                        <label>Last Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        <label>Email Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <label>Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="license_number" placeholder="DL-XXX" required>
                        <label>License Number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="allowance_flat_rate" placeholder="50.00" required>
                        <label>Flat Rate Allowance (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="status">
                            <option value="available" selected>Available</option>
                            <option value="on_trip">On Trip</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Register Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Driver Modal -->
<div class="modal fade" id="editDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Edit Driver Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.driver.edit') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="driver_id" id="edit_d_id">
                <div class="modal-body p-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="license_number" id="edit_d_license" required>
                        <label>License Number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="allowance_flat_rate" id="edit_d_allowance" required>
                        <label>Flat Rate Allowance (USD)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="status" id="edit_d_status">
                            <option value="available">Available</option>
                            <option value="on_trip">On Trip</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Booking Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.manager.update_booking') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" id="edit_b_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="pickup_address" id="edit_pickup" required>
                                <label>Pickup Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="dropoff_address" id="edit_dropoff" required>
                                <label>Dropoff Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="vehicle_id" id="edit_vehicle_id" required>
                                    <?php foreach ($vehicles as $v): ?>
                                        <option value="<?= $v->id ?>"><?= esc($v->plate_number . ' - ' . $v->model) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Vehicle</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="driver_id" id="edit_driver_id" required>
                                    <?php foreach ($drivers as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Driver</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" step="0.01" class="form-control" name="distance_km" id="edit_distance" required>
                                <label>Distance (Km)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" step="0.01" class="form-control" name="total_price" id="edit_price" required>
                                <label>Total Price ($)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="payment_status" id="edit_payment_status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="manual_verified">Manual Verified</option>
                                    <option value="refund_requested">Refund Requested</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                                <label>Payment Status</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select" name="trip_status" id="edit_trip_status">
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <label>Trip Status</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign/Change Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Assign Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.manager.assign_driver') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" id="assign_b_id">
                <div class="modal-body p-4">
                    <p class="text-muted small">Reassign or assign a driver to this safari trip booking.</p>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="driver_id" id="assign_d_id" required>
                            <option value="" disabled>Select Driver...</option>
                            <?php foreach ($drivers as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= esc($d['first_name'] . ' ' . $d['last_name']) ?> (Allowance: $<?= number_format((float)$d['allowance_flat_rate'], 2) ?>) - <?= esc(ucfirst($d['status'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label>Driver</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Collect Payment Modal -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent">Collect Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url_to('trips.manager.initiate_payment') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" id="pay_b_id">
                <div class="modal-body p-4">
                    <p class="text-muted small">Choose a payment channel to collect pending payment for this booking.</p>
                    <div class="form-floating mb-3">
                        <select class="form-select" name="provider" id="pay_provider" required>
                            <option value="mpesa" selected>Safaricom M-Pesa (STK Push)</option>
                            <option value="airtel">Airtel Money (STK Push)</option>
                            <option value="card">Credit Card / Debit Card (Paystack Page)</option>
                        </select>
                        <label>Select Provider</label>
                    </div>
                    <div class="p-3 border rounded">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Customer:</span>
                            <strong id="payCustomer">—</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Amount Due:</span>
                            <strong class="text-accent" id="payTotal">$0.00</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Initiate Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Geolocation Live Tracking Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-accent" id="trackingModalLabel">Live Tracking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div id="trackingMap" style="height: 480px; border-radius: 12px; background-color: #f0f0f0;"></div>
                <div id="trackingStatus" class="d-flex justify-content-between align-items-center mt-3 px-2 small text-muted">
                    <span>0 coordinates</span>
                    <span>Awaiting driver GPS...</span>
                    <span>Active</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll(".edit-vehicle-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("edit_v_id").value = this.getAttribute("data-id");
                document.getElementById("edit_v_plate").value = this.getAttribute("data-plate");
                document.getElementById("edit_v_model").value = this.getAttribute("data-model");
                document.getElementById("edit_v_fuel").value = this.getAttribute("data-fuel");
                document.getElementById("edit_v_margin").value = this.getAttribute("data-margin");
                document.getElementById("edit_v_reserve").value = this.getAttribute("data-reserve");
                document.getElementById("edit_v_fuel_type").value = this.getAttribute("data-fuel-type");
                document.getElementById("edit_v_status").value = this.getAttribute("data-status");
                new bootstrap.Modal(document.getElementById("editVehicleModal")).show();
            });
        });

        document.querySelectorAll(".edit-driver-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("edit_d_id").value = this.getAttribute("data-id");
                document.getElementById("edit_d_license").value = this.getAttribute("data-license");
                document.getElementById("edit_d_allowance").value = this.getAttribute("data-allowance");
                document.getElementById("edit_d_status").value = this.getAttribute("data-status");
                new bootstrap.Modal(document.getElementById("editDriverModal")).show();
            });
        });

        document.querySelectorAll(".edit-booking-driver-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("assign_b_id").value = this.getAttribute("data-booking-id");
                document.getElementById("assign_d_id").value = this.getAttribute("data-driver-id");
                new bootstrap.Modal(document.getElementById("assignDriverModal")).show();
            });
        });

        function resolvePinnedAddress(text, lat, lng) {
            if (!text.includes("Pinned Location") || lat === null || lng === null) return Promise.resolve(text);
            const formData = new FormData();
            formData.append("latitude", lat);
            formData.append("longitude", lng);
            formData.append("csrf_test_name", window.getCSRFToken());
            return fetch("<?= url_to('trips.geocode.reverse') ?>", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    window.updateCSRFToken(data.csrf_token);
                    return data.status === "success" && data.result.address ? data.result.address : text;
                })
                .catch(() => text);
        }

        document.querySelectorAll(".edit-booking-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                const pickupText = this.getAttribute("data-pickup") || "";
                const dropoffText = this.getAttribute("data-dropoff") || "";
                const pLat = this.getAttribute("data-pickup-lat");
                const pLng = this.getAttribute("data-pickup-lng");
                const dLat = this.getAttribute("data-dropoff-lat");
                const dLng = this.getAttribute("data-dropoff-lng");
                document.getElementById("edit_b_id").value = this.getAttribute("data-id");
                document.getElementById("edit_vehicle_id").value = this.getAttribute("data-vehicle");
                document.getElementById("edit_driver_id").value = this.getAttribute("data-driver");
                document.getElementById("edit_distance").value = this.getAttribute("data-distance");
                document.getElementById("edit_price").value = this.getAttribute("data-price");
                document.getElementById("edit_payment_status").value = this.getAttribute("data-payment");
                document.getElementById("edit_trip_status").value = this.getAttribute("data-trip");
                const modal = new bootstrap.Modal(document.getElementById("editBookingModal"));
                Promise.all([
                    resolvePinnedAddress(pickupText, pLat, pLng),
                    resolvePinnedAddress(dropoffText, dLat, dLng)
                ]).then(([pickup, dropoff]) => {
                    document.getElementById("edit_pickup").value = pickup;
                    document.getElementById("edit_dropoff").value = dropoff;
                    modal.show();
                });
            });
        });

        document.querySelectorAll(".collect-payment-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("pay_b_id").value = this.getAttribute("data-id");
                document.getElementById("payCustomer").innerText = this.getAttribute("data-customer");
                document.getElementById("payTotal").innerText = "$" + this.getAttribute("data-total");
                new bootstrap.Modal(document.getElementById("collectPaymentModal")).show();
            });
        });

        document.querySelectorAll(".track-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                const bookingId = this.getAttribute("data-id");
                const pLat = parseFloat(this.getAttribute("data-pickup-lat"));
                const pLng = parseFloat(this.getAttribute("data-pickup-lng"));
                const dLat = parseFloat(this.getAttribute("data-dropoff-lat"));
                const dLng = parseFloat(this.getAttribute("data-dropoff-lng"));
                window._pendingTracking = {
                    bookingId,
                    pLat,
                    pLng,
                    dLat,
                    dLng
                };
                if (typeof google === 'undefined') {
                    loadGoogleMapsAPI().then(() => {
                            new bootstrap.Modal(document.getElementById("trackingModal")).show();
                        })
                        .catch(err => {
                            console.error(err);
                            alert("Unable to load map.");
                            window._pendingTracking = null;
                        });
                } else {
                    new bootstrap.Modal(document.getElementById("trackingModal")).show();
                }
            });
        });
    });

    let mapsLoaded = false,
        mapsLoadPromise = null;

    function loadGoogleMapsAPI() {
        if (mapsLoaded) return Promise.resolve();
        if (mapsLoadPromise) return mapsLoadPromise;
        mapsLoadPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=<?= esc($googleApiKey) ?>&callback=initManagerTrackingCallback';
            script.async = true;
            script.defer = true;
            window.initManagerTrackingCallback = function() {
                mapsLoaded = true;
                resolve();
            };
            script.onerror = function() {
                reject(new Error("Google Maps API failed to load"));
            };
            document.head.appendChild(script);
        });
        return mapsLoadPromise;
    }

    let trackingMap, pathPolyline, driverMarker, intervalId;
    let pickupMarker, dropoffMarker;

    document.getElementById("trackingModal").addEventListener("shown.bs.modal", function() {
        if (!window._pendingTracking) return;
        const {
            bookingId,
            pLat,
            pLng,
            dLat,
            dLng
        } = window._pendingTracking;
        window._pendingTracking = null;
        if (intervalId) clearInterval(intervalId);

        trackingMap = new google.maps.Map(document.getElementById("trackingMap"), {
            zoom: 13,
            center: {
                lat: pLat,
                lng: pLng
            },
            disableDefaultUI: false,
        });

        pickupMarker = new google.maps.Marker({
            position: {
                lat: pLat,
                lng: pLng
            },
            map: trackingMap,
            title: "Pickup",
            icon: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
        });
        dropoffMarker = new google.maps.Marker({
            position: {
                lat: dLat,
                lng: dLng
            },
            map: trackingMap,
            title: "Dropoff",
            icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
        });
        driverMarker = new google.maps.Marker({
            position: {
                lat: pLat,
                lng: pLng
            },
            map: trackingMap,
            title: "Driver Location",
            icon: "https://maps.google.com/mapfiles/kml/pal2/icon47.png"
        });

        pathPolyline = new google.maps.Polyline({
            path: [],
            geodesic: true,
            strokeColor: "#0d6efd",
            strokeOpacity: 1.0,
            strokeWeight: 4,
            map: trackingMap
        });

        const directionsService = new google.maps.DirectionsService();
        directionsService.route({
            origin: {
                lat: pLat,
                lng: pLng
            },
            destination: {
                lat: dLat,
                lng: dLng
            },
            travelMode: google.maps.TravelMode.DRIVING
        }, (result, status) => {
            if (status === 'OK') {
                new google.maps.Polyline({
                    path: result.routes[0].overview_path,
                    geodesic: true,
                    strokeColor: '#adb5bd',
                    strokeOpacity: 0.6,
                    strokeWeight: 3,
                    map: trackingMap
                });
            }
        });

        google.maps.event.trigger(trackingMap, 'resize');
        loadTrackingCoordinates(bookingId);
        intervalId = setInterval(() => loadTrackingCoordinates(bookingId), 15000);
        document.getElementById("trackingModal").addEventListener("hidden.bs.modal", function() {
            clearInterval(intervalId);
        }, {
            once: true
        });
    });

    function loadTrackingCoordinates(bookingId) {
        fetch(`<?= base_url('trips/tracking/coordinates') ?>/${bookingId}`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                const status = document.getElementById("trackingStatus");
                if (data.status === "success") {
                    const path = data.result.map(coord => ({
                        lat: parseFloat(coord.latitude),
                        lng: parseFloat(coord.longitude)
                    }));
                    if (path.length > 0) {
                        pathPolyline.setPath(path);
                        driverMarker.setPosition(path[path.length - 1]);
                        trackingMap.setCenter(path[path.length - 1]);
                        const lastRaw = data.result[data.result.length - 1].created_at;
                        const diffSec = Math.floor((Date.now() - new Date(lastRaw.replace(' ', 'T') + 'Z').getTime()) / 1000);
                        const ago = diffSec < 60 ? `${diffSec}s ago` : `${Math.floor(diffSec / 60)}m ago`;
                        status.innerHTML = `<span>${path.length} coordinate${path.length !== 1 ? 's' : ''}</span><span class="text-accent">Last: ${ago}</span><span>Driver moving</span>`;
                    } else {
                        status.innerHTML = `<span>0 coordinates</span><span class="text-warning">No GPS yet</span><span>Active</span>`;
                    }
                } else {
                    status.innerHTML = `<span>--</span><span class="text-danger">Fetch error</span><span>Active</span>`;
                }
            })
            .catch(() => {
                document.getElementById("trackingStatus").innerHTML = `<span>--</span><span class="text-danger">Connection lost</span><span>Active</span>`;
            });
    }
</script>
<?= $this->endSection() ?>