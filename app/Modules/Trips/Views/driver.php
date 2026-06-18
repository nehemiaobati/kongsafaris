<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Active Trip Map Route Column (visible when there is an active trip) -->
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
            <div class="card blueprint-card p-3 h-100">
                <h5 class="fw-bold text-accent mb-2">Live Navigation Route</h5>
                <div id="driverRouteMap" style="min-height: 400px; border-radius: 12px; background-color: #222;"></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Booking Details Column -->
    <div class="<?= $activeBooking !== null ? 'col-lg-6' : 'col-md-8 mx-auto' ?> mb-4">
        <div class="card blueprint-card p-4 h-100">
            <h4 class="fw-bold text-accent mb-2">Driver Workspace</h4>
            <p class="text-secondary small">View and manage your assigned safari transfers. Starting a trip initiates real-time GPS coordinate transmission.</p>

            <hr class="border-secondary my-3">

            <?php if (empty($bookings)): ?>
                <div class="text-center py-5 text-muted">
                    <span class="fs-1"></span>
                    <p class="mt-2 mb-0">No active or pending safari transfers assigned to you.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="p-3 mb-3 border border-secondary border-opacity-25 rounded bg-dark bg-opacity-25">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-secondary mb-1">Trip #<?= esc($booking->id) ?></span>
                                    <h5 class="fw-bold mb-1 text-light"><?= esc($booking->plate_number) ?> - <?= esc($booking->model) ?></h5>
                                </div>
                                <div>
                                    <?php if ($booking->trip_status === 'active'): ?>
                                        <span class="badge bg-success animate-pulse px-3 py-2">Trip Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-3 py-2">Assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="small mb-3">
                                <strong>Pickup:</strong> <?= esc($booking->pickup_address) ?><br>
                                <strong>Destination:</strong> <?= esc($booking->dropoff_address) ?><br>
                                <strong>Distance:</strong> <?= esc($booking->distance_km) ?> Km
                            </div>

                            <!-- Native Navigation Link Option -->
                            <div class="mb-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode((string)$booking->dropoff_latitude . ',' . (string)$booking->dropoff_longitude) ?>&waypoints=<?= urlencode((string)$booking->pickup_latitude . ',' . (string)$booking->pickup_longitude) ?>&travelmode=driving"
                                    target="_blank"
                                    class="btn btn-outline-warning btn-sm w-100 py-2">
                                    Open in Native Google Maps App
                                </a>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($booking->trip_status === 'active'): ?>
                                        <div class="text-success small d-flex align-items-center">
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
                                        <button type="submit" class="btn btn-success px-4">Start Safari Trip</button>
                                    <?php elseif ($booking->trip_status === 'active'): ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-info px-4">Complete Trip</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
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
                    strokeColor: "#d4af37",
                    strokeWeight: 5
                }
            });

            driverMap = new google.maps.Map(document.getElementById("driverRouteMap"), {
                zoom: 12,
                center: {
                    lat: pLat,
                    lng: pLng
                },
                styles: [{
                        elementType: "geometry",
                        stylers: [{
                            color: "#1f2721"
                        }]
                    },
                    {
                        elementType: "labels.text.stroke",
                        stylers: [{
                            color: "#1f2721"
                        }]
                    },
                    {
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#748077"
                        }]
                    },
                    {
                        featureType: "road",
                        elementType: "geometry",
                        stylers: [{
                            color: "#2d382f"
                        }]
                    },
                    {
                        featureType: "water",
                        elementType: "geometry",
                        stylers: [{
                            color: "#0d1c13"
                        }]
                    }
                ]
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
            const activeBadge = document.querySelector('.badge.bg-secondary');
            if (activeBadge) {
                const bookingId = parseInt(activeBadge.innerText.replace('Trip #', ''));
                if (bookingId) {
                    startGPSWatchLoop(bookingId);
                }
            }
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

                // Update driver location marker dynamically on map
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