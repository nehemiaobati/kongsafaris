<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
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

    .table-hover tbody tr:hover {
        background-color: rgba(var(--theme-accent-rgb), 0.02) !important;
        transition: background-color 0.2s ease-in-out;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4 align-items-center">
    <div class="col-sm-8">
        <h2 class="fw-bold text-accent mb-1">My Safaris & Booking History</h2>
        <p class="text-muted small mb-0">Monitor your paid bookings, real-time trip progress, and cancellation refunds.</p>
    </div>
    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
        <a href="<?= url_to('trips.quote') ?>" class="btn btn-primary px-4">Book a New Safari</a>
    </div>
</div>

<div class="card blueprint-card p-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Driver / Vehicle</th>
                    <th>Route Details</th>
                    <th>Distance & Cost</th>
                    <th>Payment Status</th>
                    <th>Trip Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div class="py-4">
                                <h5 class="fw-bold mb-1">No Bookings Found</h5>
                                <p class="text-muted small mb-3">You haven't booked any safari trips yet. Start your adventure today!</p>
                                <a href="<?= url_to('trips.quote') ?>" class="btn btn-primary btn-sm px-4">Get a Quote</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?= esc($booking->id) ?></strong></td>
                            <td>
                                <div class="small">
                                    <strong><?= esc($booking->first_name . ' ' . $booking->last_name) ?></strong><br>
                                    <span class="text-muted"><?= esc($booking->model) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="small text-truncate" style="max-width: 250px;">
                                    <strong>From:</strong> <?= esc($booking->pickup_address) ?><br>
                                    <strong>To:</strong> <?= esc($booking->dropoff_address) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary mb-1"><?= esc($booking->distance_km) ?> Km</span><br>
                                <strong class="text-accent">Ksh <?= number_format($booking->total_price, 2) ?></strong>
                            </td>
                            <td>
                                <?php if ($booking->payment_status === 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif ($booking->payment_status === 'refund_requested'): ?>
                                    <span class="badge bg-info">Refund Requested</span>
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
                                <?php if ($booking->trip_status === 'pending'): ?>
                                    <form action="<?= url_to('trips.booking.cancel') ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking and request a refund?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="booking_id" value="<?= $booking->id ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Cancel Booking</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>Non-cancellable</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>