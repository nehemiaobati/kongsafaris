<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-accent mb-1">Reports & Analytics</h2>
        <p class="text-muted small mb-0">Revenue summaries, realized/unrealized gains, vehicle profitability, and fuel cost trends.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url_to('trips.reports.csv') ?>?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-outline-secondary btn-sm">Export CSV</a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card blueprint-card p-3 mb-4">
    <form method="GET" action="<?= url_to('trips.reports') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="date" class="form-control" id="startDate" name="start_date" value="<?= $start_date ?>" required>
                <label for="startDate">Start Date</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="date" class="form-control" id="endDate" name="end_date" value="<?= $end_date ?>" required>
                <label for="endDate">End Date</label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Analysis Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card blueprint-card p-3 h-100 border-success">
            <h6 class="text-success fw-bold text-uppercase small mb-2">Completed Trips (Realized Gains)</h6>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Trips</span>
                <strong><?= esc($completed_analysis['completed_trips'] ?? 0) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Gross Revenue</span>
                <strong class="text-accent">Ksh <?= number_format((float)($completed_analysis['completed_gross_revenue'] ?? 0), 2) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Total Costs</span>
                <strong class="text-warning">Ksh <?= number_format((float)($completed_analysis['completed_total_costs'] ?? 0), 2) ?></strong>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-success fw-bold">Net Profit</span>
                <span class="fs-5 fw-bold text-success">Ksh <?= number_format((float)($completed_analysis['completed_net_profit'] ?? 0), 2) ?></span>
            </div>
            <div class="d-flex justify-content-between small mt-2 text-muted">
                <span>Avg distance</span>
                <span><?= number_format((float)($completed_analysis['avg_distance'] ?? 0), 1) ?> Km</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card blueprint-card p-3 h-100 border-warning">
            <h6 class="text-warning fw-bold text-uppercase small mb-2">Uncompleted Trips (Unrealized Gains)</h6>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Total Uncompleted</span>
                <strong><?= esc($uncompleted_analysis['uncompleted_trips'] ?? 0) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Cancelled</span>
                <strong class="text-danger"><?= esc($uncompleted_analysis['cancelled_trips'] ?? 0) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Pending</span>
                <strong class="text-warning"><?= esc($uncompleted_analysis['pending_trips'] ?? 0) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Active</span>
                <strong class="text-primary"><?= esc($uncompleted_analysis['active_trips'] ?? 0) ?></strong>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-warning fw-bold">At-Risk Revenue</span>
                <span class="fs-5 fw-bold text-warning">Ksh <?= number_format((float)($uncompleted_analysis['at_risk_revenue'] ?? 0), 2) ?></span>
            </div>
            <div class="d-flex justify-content-between small mt-2 text-muted">
                <span>Potential lost</span>
                <span>Ksh <?= number_format((float)($uncompleted_analysis['unrealized_potential_revenue'] ?? 0), 2) ?></span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card blueprint-card p-3 h-100 border-danger">
            <h6 class="text-danger fw-bold text-uppercase small mb-2">Refunds (Realized Losses)</h6>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Refunded Trips</span>
                <strong><?= esc($refund_analysis['refunded_trips'] ?? 0) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Total Refunded</span>
                <strong class="text-danger">Ksh <?= number_format((float)($refund_analysis['total_refunded'] ?? 0), 2) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Avg Refund</span>
                <strong>Ksh <?= number_format((float)($refund_analysis['avg_refund_amount'] ?? 0), 2) ?></strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Pending Requests</span>
                <strong class="text-warning"><?= esc($refund_analysis['pending_refund_requests'] ?? 0) ?></strong>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-danger fw-bold">Refund Rate</span>
                <span class="fs-5 fw-bold text-danger"><?= number_format((float)($refund_analysis['refund_rate'] ?? 0), 2) ?>%</span>
            </div>
            <div class="d-flex justify-content-between small mt-2 text-muted">
                <span>Of gross revenue</span>
                <span><?= number_format((float)($refund_analysis['refund_rate'] ?? 0), 2) ?>%</span>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card blueprint-card p-3 text-center h-100">
            <span class="text-muted small">Total Revenue</span>
            <h3 class="fw-bold text-accent mt-1">Ksh <?= number_format((float)($summary['total_revenue'] ?? 0), 2) ?></h3>
            <span class="text-success small"><?= (int)($summary['total_trips'] ?? 0) ?> trips</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card blueprint-card p-3 text-center h-100">
            <span class="text-muted small">Net Profit</span>
            <h3 class="fw-bold text-success mt-1">
                Ksh <?= number_format((float)($summary['total_revenue'] ?? 0) - (float)($summary['total_fuel_cost'] ?? 0) - (float)($summary['total_maintenance'] ?? 0) - (float)($summary['total_allowances'] ?? 0), 2) ?>
            </h3>
            <span class="text-muted small">After costs</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card blueprint-card p-3 text-center h-100">
            <span class="text-muted small">Total Distance</span>
            <h3 class="fw-bold mt-1"><?= number_format((float)($summary['total_distance'] ?? 0), 0) ?> Km</h3>
            <span class="text-muted small">Across all trips</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card blueprint-card p-3 text-center h-100">
            <span class="text-muted small">Fuel Costs</span>
            <h3 class="fw-bold text-warning mt-1">Ksh <?= number_format((float)($summary['total_fuel_cost'] ?? 0), 2) ?></h3>
            <span class="text-muted small">+ Ksh <?= number_format((float)($summary['total_maintenance'] ?? 0), 2) ?> maintenance</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card blueprint-card p-4 h-100">
            <h5 class="fw-bold mb-3">Daily Revenue Trend</h5>
            <canvas id="trendChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card blueprint-card p-4 h-100">
            <h5 class="fw-bold mb-3">Fuel Price Trend</h5>
            <canvas id="fuelChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Completed Trips Detail Table -->
<div class="card blueprint-card p-4 mb-4">
    <h5 class="fw-bold text-success mb-3">Completed Trips Detail (Realized Gains)</h5>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Trips Completed</td>
                    <td><strong><?= esc($completed_analysis['completed_trips'] ?? 0) ?></strong></td>
                </tr>
                <tr>
                    <td>Total Distance</td>
                    <td><strong><?= number_format((float)($completed_analysis['completed_distance'] ?? 0), 2) ?> Km</strong></td>
                </tr>
                <tr>
                    <td>Gross Revenue</td>
                    <td><strong class="text-accent">Ksh <?= number_format((float)($completed_analysis['completed_gross_revenue'] ?? 0), 2) ?></strong></td>
                </tr>
                <tr>
                    <td>Total Costs (Fuel + Maint + Allowance)</td>
                    <td><strong class="text-warning">Ksh <?= number_format((float)($completed_analysis['completed_total_costs'] ?? 0), 2) ?></strong></td>
                </tr>
                <tr>
                    <td>Net Profit (Realized)</td>
                    <td><strong class="text-success">Ksh <?= number_format((float)($completed_analysis['completed_net_profit'] ?? 0), 2) ?></strong></td>
                </tr>
                <tr>
                    <td>Average Trip Distance</td>
                    <td><strong><?= number_format((float)($completed_analysis['avg_distance'] ?? 0), 1) ?> Km</strong></td>
                </tr>
                <tr>
                    <td>Average Trip Value</td>
                    <td><strong>Ksh <?= number_format((float)($completed_analysis['avg_trip_value'] ?? 0), 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- By Vehicle Table -->
<div class="card blueprint-card p-4 mb-4">
    <h5 class="fw-bold text-accent mb-3">Revenue by Vehicle</h5>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Trips</th>
                    <th>Distance (Km)</th>
                    <th>Gross Revenue</th>
                    <th>Fuel Cost</th>
                    <th>Maintenance</th>
                    <th>Allowances</th>
                    <th>Net Profit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($by_vehicle)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No completed trips in this period.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($by_vehicle as $v): ?>
                        <?php $net = (float)$v['gross_revenue'] - (float)$v['total_fuel'] - (float)$v['total_maintenance'] - (float)$v['total_allowances']; ?>
                        <tr>
                            <td><strong><?= esc($v['model']) ?></strong><br><small class="text-muted"><?= esc($v['plate_number']) ?></small></td>
                            <td><?= $v['trip_count'] ?></td>
                            <td><?= number_format((float)$v['total_km'], 1) ?></td>
                            <td class="text-accent fw-bold">Ksh <?= number_format((float)$v['gross_revenue'], 2) ?></td>
                            <td>Ksh <?= number_format((float)$v['total_fuel'], 2) ?></td>
                            <td>Ksh <?= number_format((float)$v['total_maintenance'], 2) ?></td>
                            <td>Ksh <?= number_format((float)$v['total_allowances'], 2) ?></td>
                            <td class="fw-bold <?= $net >= 0 ? 'text-success' : 'text-danger' ?>">Ksh <?= number_format($net, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- By Driver Table -->
<div class="card blueprint-card p-4 mb-4">
    <h5 class="fw-bold text-accent mb-3">Revenue by Driver</h5>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Trips</th>
                    <th>Distance (Km)</th>
                    <th>Gross Revenue</th>
                    <th>Allowances Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($by_driver)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No completed trips in this period.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($by_driver as $d): ?>
                        <tr>
                            <td><strong><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></strong></td>
                            <td><?= $d['trip_count'] ?></td>
                            <td><?= number_format((float)$d['total_km'], 1) ?></td>
                            <td class="text-accent fw-bold">Ksh <?= number_format((float)$d['gross_revenue'], 2) ?></td>
                            <td>Ksh <?= number_format((float)$d['total_allowances'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = <?= json_encode($trend) ?>;

        if (trendData.length > 0) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.date),
                    datasets: [{
                        label: 'Daily Revenue (Ksh)',
                        data: trendData.map(d => parseFloat(d.daily_revenue)),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#212529'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#6c757d',
                                callback: v => 'Ksh ' + v
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
        } else {
            trendCtx.canvas.parentNode.innerHTML = '<p class="text-muted text-center py-4">No revenue data available for this period.</p>';
        }

        const fuelCtx = document.getElementById('fuelChart').getContext('2d');
        const fuelData = <?= json_encode($fuel_trend) ?>;

        if (fuelData.length > 0) {
            new Chart(fuelCtx, {
                type: 'line',
                data: {
                    labels: fuelData.map(d => d.month),
                    datasets: [{
                        label: 'Price per Liter (Ksh)',
                        data: fuelData.map(d => parseFloat(d.price_per_liter)),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#212529'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#6c757d',
                                callback: v => 'Ksh ' + v
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        }
                    }
                }
            });
        } else {
            fuelCtx.canvas.parentNode.innerHTML = '<p class="text-muted text-center py-4">No fuel price history available.</p>';
        }
    });
</script>
<?= $this->endSection() ?>