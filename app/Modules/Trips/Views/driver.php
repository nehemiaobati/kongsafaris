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

    .trip-card {
        background: rgba(var(--theme-accent-rgb), 0.01);
        border: 1px solid rgba(var(--theme-primary-rgb), 0.08);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .trip-card:hover {
        border-color: rgba(var(--theme-accent-rgb), 0.3);
        background: rgba(var(--theme-accent-rgb), 0.02);
        box-shadow: 0 4px 12px rgba(var(--theme-primary-rgb), 0.03);
    }

    .trip-badge-id {
        font-family: monospace;
        font-weight: 700;
        background-color: rgba(var(--theme-primary-rgb), 0.05);
        color: var(--theme-primary);
        border: 1px solid rgba(var(--theme-primary-rgb), 0.1);
        padding: 4px 8px;
        border-radius: 6px;
    }

    .route-line {
        position: relative;
        padding-left: 5px;
    }

    .route-line::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        border-left: 2px dashed rgba(var(--theme-primary-rgb), 0.2);
    }

    .route-dot {
        position: absolute;
        left: 2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .route-dot.pickup {
        top: 6px;
        background: var(--bs-success);
        box-shadow: 0 0 0 3px rgba(var(--bs-success-rgb), 0.2);
    }

    .route-dot.dropoff {
        top: 6px;
        background: var(--theme-accent);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
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
        <h2 class="fw-bold text-accent mb-1">Driver Workspace</h2>
        <p class="text-muted small mb-0">View and manage your assigned safari transfers, track live coordinate logging, and view completed trips history.</p>
    </div>
    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
        <span class="badge bg-success-subtle text-success border border-success-subtle p-2 px-3 fw-semibold">
            On Duty
        </span>
    </div>
</div>

<div class="row">
    <?php
    $activeBooking = null;
    foreach ($bookings as $b) {
        if ($b->trip_status === 'active') {
            $activeBooking = $b;
            break;
        }
    }
    ?>

    <?php if ($activeBooking !== null): ?>
        <div class="col-lg-6 mb-4">
            <div class="card blueprint-card p-3 ">
                <h5 class="fw-bold text-accent mb-2">Live Navigation Route</h5>
                <div id="driverRouteMap" style="min-height: 400px; border-radius: 12px; background-color: #f0f0f0;"></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="<?= $activeBooking !== null ? 'col-lg-6' : 'col-12' ?> mb-4">
        <div class="card blueprint-card p-4 ">
            <h4 class="fw-bold text-accent mb-2">Assigned Transfers</h4>
            <p class="text-muted small">Starting a trip initiates real-time GPS coordinate transmission to the manager portal.</p>

            <hr class="my-3">

            <?php if (empty($bookings)): ?>
                <div class="text-center py-5 text-muted">
                    <p class="mt-2 mb-0">No active or pending safari transfers assigned to you.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="trip-card p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="trip-badge-id mb-2 d-inline-block">Trip #<?= esc($booking->id) ?></span>
                                    <h5 class="fw-bold mb-0 text-dark"><?= esc($booking->plate_number) ?></h5>
                                    <span class="text-muted small"><?= esc($booking->model) ?></span>
                                </div>
                                <div>
                                    <?php if ($booking->trip_status === 'active'): ?>
                                        <span class="badge bg-success animate-pulse px-3 py-2 fw-semibold">Trip Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-3 py-2 fw-semibold">Assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="route-line mb-3">
                                <div class="position-relative ps-4 pb-3">
                                    <span class="route-dot pickup"></span>
                                    <span class="text-muted small d-block fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">PICKUP ADDRESS</span>
                                    <span class="text-dark small"><?= esc($booking->pickup_address) ?></span>
                                </div>
                                <div class="position-relative ps-4">
                                    <span class="route-dot dropoff"></span>
                                    <span class="text-muted small d-block fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">DROP-OFF DESTINATION</span>
                                    <span class="text-dark small"><?= esc($booking->dropoff_address) ?></span>
                                </div>
                            </div>

                            <div class="row g-2 mb-3 bg-light rounded p-2 text-center">
                                <div class="col-6 border-end">
                                    <span class="text-muted small d-block">Distance</span>
                                    <strong class="text-dark"><?= esc($booking->distance_km) ?> Km</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">Allowance</span>
                                    <strong class="text-accent">Ksh <?= number_format((float)$booking->driver_allowance, 2) ?></strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode((string)$booking->dropoff_latitude . ',' . (string)$booking->dropoff_longitude) ?>&waypoints=<?= urlencode((string)$booking->pickup_latitude . ',' . (string)$booking->pickup_longitude) ?>&travelmode=driving"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm w-100 py-2 fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i> Open in Google Maps
                                </a>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <div>
                                    <?php if ($booking->trip_status === 'active'): ?>
                                        <div class="text-success small d-flex align-items-center fw-medium">
                                            <span class="spinner-grow spinner-grow-sm me-2" role="status"></span>
                                            <span>GPS Live Logging Active...</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <form action="<?= url_to('trips.driver.status') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="booking_id" value="<?= $booking->id ?>">
                                    <?php if ($booking->trip_status === 'pending'): ?>
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-success px-4 fw-semibold">Start Safari Trip</button>
                                    <?php elseif ($booking->trip_status === 'active'): ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-info text-white px-4 fw-semibold">Complete Trip</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
        </div>

        <div class="card blueprint-card p-4 mt-4">
            <h4 class="fw-bold text-accent mb-2">Trip Logs & History</h4>
            <p class="text-muted small">View your completed and cancelled safari transfers.</p>
            <hr class="my-3">

            <?php if (empty($pastBookings)): ?>
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">No past safari transfers found in your logs.</p>
                </div>
            <?php else: ?>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="driverHistorySearchInput" placeholder="Search trip logs...">
                    <label for="driverHistorySearchInput">Search trip logs...</label>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="driverHistoryTable">
                        <thead>
                            <tr>
                                <th>Trip ID</th>
                                <th>Vehicle</th>
                                <th>Route Details</th>
                                <th>Distance</th>
                                <th>Allowance</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastBookings as $past): ?>
                                <tr>
                                    <td><strong>#<?= esc($past->id) ?></strong></td>
                                    <td>
                                        <div class="small">
                                            <strong><?= esc($past->plate_number) ?></strong><br>
                                            <span class="text-muted"><?= esc($past->model) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-truncate" style="max-width: 250px;">
                                            <strong>From:</strong> <?= esc($past->pickup_address) ?><br>
                                            <strong>To:</strong> <?= esc($past->dropoff_address) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($past->distance_km) ?> Km</span>
                                    </td>
                                    <td>
                                        <strong class="text-accent">Ksh <?= number_format((float)$past->driver_allowance, 2) ?></strong>
                                    </td>
                                    <td>
                                        <span class="small text-muted"><?= esc(date('M d, Y h:i A', strtotime($past->created_at))) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($past->trip_status === 'completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($activeBooking !== null): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GoogleMaps.APIKey') ?? '') ?>&callback=initDriverMap" defer></script>
<?php endif; ?>

<script>
    let driverMap, directionsService, directionsRenderer;
    let driverLocationMarker = null;

    function initDriverMap() {
        <?php if ($activeBooking !== null): ?>
            const pLat = parseFloat("<?= $activeBooking->pickup_latitude ?>");
            const pLng = parseFloat("<?= $activeBooking->pickup_longitude ?>");
            const dLat = parseFloat("<?= $activeBooking->dropoff_latitude ?>");
            const dLng = parseFloat("<?= $activeBooking->dropoff_longitude ?>");

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                polylineOptions: {
                    strokeColor: "#0d6efd",
                    strokeWeight: 5
                }
            });

            driverMap = new google.maps.Map(document.getElementById("driverRouteMap"), {
                zoom: 12,
                center: {
                    lat: pLat,
                    lng: pLng
                }
            });

            directionsRenderer.setMap(driverMap);

            directionsService.route({
                origin: new google.maps.LatLng(pLat, pLng),
                destination: new google.maps.LatLng(dLat, dLng),
                travelMode: google.maps.TravelMode.DRIVING
            }, (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                }
            });
        <?php endif; ?>
    }

    document.addEventListener("DOMContentLoaded", function() {
        const activeTripElements = document.querySelectorAll('.animate-pulse');
        if (activeTripElements.length > 0) {
            const activeBadge = document.querySelector('.trip-badge-id');
            if (activeBadge) {
                const bookingId = parseInt(activeBadge.innerText.replace('Trip #', ''));
                if (bookingId) {
                    startGPSWatchLoop(bookingId);
                }
            }
        }

        // Driver History Search
        const searchInput = document.getElementById('driverHistorySearchInput');
        const table = document.getElementById('driverHistoryTable');
        if (searchInput && table) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase().trim();
                const rows = table.querySelectorAll('tbody tr:not(.no-matches-row)');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.setProperty('display', '', 'important');
                        visibleCount++;
                    } else {
                        row.style.setProperty('display', 'none', 'important');
                    }
                });
                
                const existingMsg = table.querySelector('.no-matches-row');
                if (existingMsg) {
                    existingMsg.remove();
                }
                
                if (visibleCount === 0 && filter !== '') {
                    const tbody = table.querySelector('tbody');
                    if (tbody) {
                        const colsCount = table.querySelectorAll('thead th').length || 7;
                        const tr = document.createElement('tr');
                        tr.className = 'no-matches-row';
                        tr.innerHTML = `<td colspan="${colsCount}" class="text-center text-muted py-4">No matching safari transfers found.</td>`;
                        tbody.appendChild(tr);
                    }
                }
            });
        }
    });

    let gpsWatchId = null;
    let lastTransmissionTime = 0;

    function startGPSWatchLoop(bookingId) {
        if (!("geolocation" in navigator)) {
            console.error("Geolocation is not supported by this browser.");
            return;
        }

        gpsWatchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (typeof driverMap !== 'undefined' && driverMap) {
                    const driverLatLng = new google.maps.LatLng(lat, lng);
                    if (driverLocationMarker) {
                        driverLocationMarker.setPosition(driverLatLng);
                    } else {
                        driverLocationMarker = new google.maps.Marker({
                            position: driverLatLng,
                            map: driverMap,
                            title: "Your Location",
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: "#0d6efd",
                                fillOpacity: 0.9,
                                strokeColor: "#ffffff",
                                strokeWeight: 2
                            }
                        });
                    }
                }

                const now = Date.now();
                if (now - lastTransmissionTime >= 60000) {
                    transmitCoordinates(bookingId, lat, lng);
                    lastTransmissionTime = now;
                }
            },
            (error) => {
                console.error("GPS Watch Error: ", error);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    function transmitCoordinates(bookingId, lat, lng) {
        const formData = new FormData();
        formData.append("booking_id", bookingId);
        formData.append("latitude", lat);
        formData.append("longitude", lng);
        formData.append("csrf_test_name", window.getCSRFToken());

        fetch("<?= url_to('trips.tracking.update') ?>", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                window.updateCSRFToken(data.csrf_token);
                if (data.status === "success") {
                    console.log(`GPS logged: Lat ${lat}, Lng ${lng}`);
                }
            })
            .catch(err => console.error("Coordinates upload failed", err));
    }
</script>
<?= $this->endSection() ?>