<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    /* Google Autocomplete Suggestions Dark Mode Override */
    .pac-container {
        background-color: #1a231b !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5) !important;
        font-family: inherit !important;
    }

    .pac-item {
        border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
        color: #f1f3f2 !important;
        padding: 8px 12px !important;
    }

    .pac-item:hover {
        background-color: rgba(212, 175, 55, 0.1) !important;
    }

    .pac-item-query {
        color: #ffffff !important;
    }

    .pac-matched {
        color: var(--safari-accent) !important;
    }

    .pac-icon {
        filter: invert(1);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Map Column -->
    <div class="col-lg-7 mb-4">
        <div class="card blueprint-card h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0 text-accent">Safari Route Map</h5>
                <span class="text-secondary small">Click the map directly to drop pin markers.</span>
            </div>

            <!-- Map pinning selector -->
            <div class="card bg-dark bg-opacity-50 border-secondary border-opacity-25 p-2 mb-3 rounded">
                <span class="text-secondary small d-block mb-1">Map Pin Placement Mode:</span>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pin_mode" id="pinPickup" value="pickup" checked>
                        <label class="form-check-label text-light small" for="pinPickup">Pin Pickup Location</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pin_mode" id="pinDropoff" value="dropoff">
                        <label class="form-check-label text-light small" for="pinDropoff">Pin Destination</label>
                    </div>
                </div>
            </div>

            <div id="bookingMap" style="min-height: 440px; border-radius: 12px; background-color: #222;"></div>
        </div>
    </div>

    <!-- Booking Form & Quote Details Column -->
    <div class="col-lg-5 mb-4">
        <div class="card blueprint-card p-4 h-100">
            <h4 class="fw-bold text-accent mb-1">Request a Safari Quote</h4>
            <p class="text-muted small mb-4">Select vehicle and destinations to get instant dynamic pricing.</p>

            <form id="quoteForm" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-dark border-secondary text-light" id="pickupInput" name="pickup_address" placeholder="Pickup Address" required>
                    <label for="pickupInput" class="text-secondary">Pickup Location</label>
                    <input type="hidden" id="p_lat" name="pickup_latitude">
                    <input type="hidden" id="p_lng" name="pickup_longitude">
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-dark border-secondary text-light" id="dropoffInput" name="dropoff_address" placeholder="Dropoff Address" required>
                    <label for="dropoffInput" class="text-secondary">Destination</label>
                    <input type="hidden" id="d_lat" name="dropoff_latitude">
                    <input type="hidden" id="d_lng" name="dropoff_longitude">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select bg-dark border-secondary text-light" id="vehicleSelect" name="vehicle_id" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?= $vehicle->id ?>" data-model="<?= esc($vehicle->model) ?>">
                                        <?= esc($vehicle->model) ?> (<?= esc($vehicle->plate_number) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="vehicleSelect" class="text-secondary">Vehicle Class</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select bg-dark border-secondary text-light" id="driverSelect" name="driver_id" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php foreach ($drivers as $driver): ?>
                                    <option value="<?= $driver['id'] ?>">
                                        <?= esc($driver['first_name'] . ' ' . $driver['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="driverSelect" class="text-secondary">Assigned Driver</label>
                        </div>
                    </div>
                </div>

                <!-- Live Price Summary -->
                <div id="priceSummary" class="p-3 mb-4 rounded bg-success bg-opacity-10 border border-success border-opacity-25" style="display: none;">
                    <h6 class="fw-bold text-accent mb-3">Cost Summary</h6>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Distance:</span>
                        <strong id="summaryDistance">0.00 Km</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Base booking fee:</span>
                        <strong id="summaryBase">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Est. fuel cost:</span>
                        <strong id="summaryFuel">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Maintenance reserve:</span>
                        <strong id="summaryMaint">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Driver allowance:</span>
                        <strong id="summaryDriver">$0.00</strong>
                    </div>
                    <hr class="my-2 border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Price:</span>
                        <span class="fs-4 fw-bold text-accent" id="summaryTotal">$0.00</span>
                    </div>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="button" id="bookBtn" disabled>Confirm & Book Safari</button>
            </form>
        </div>
    </div>
</div>

<!-- Paystack Providers Checkout Modal -->
<div class="modal fade" id="paymentModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border border-secondary">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title fw-bold text-accent" id="paymentModalLabel">Complete Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Verify details and select your preferred payment channel below.</p>
                <div class="mb-3">
                    <span class="text-secondary small">Total Booking cost:</span>
                    <h3 class="fw-bold text-accent mt-1" id="modalTotal">$0.00</h3>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select bg-dark border-secondary text-light" id="providerSelect" required>
                        <option value="mpesa" selected>Safaricom M-Pesa (STK Push)</option>
                        <option value="airtel">Airtel Money (STK Push)</option>
                        <option value="card">Credit Card / Debit Card (Paystack Page)</option>
                    </select>
                    <label for="providerSelect" class="text-secondary">Select Provider</label>
                </div>

                <div id="paymentAlert" class="alert alert-info py-2 small" style="display: none;"></div>
                <button class="btn btn-primary w-100 btn-lg mt-2" id="payBtn" type="button">Trigger Paystack Authorization</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Google Maps Platform integration -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc($googleApiKey) ?>&libraries=places&callback=initBookingInterface" defer></script>
<script>
    let map, directionsService, directionsRenderer;
    let pickupAutocomplete, dropoffAutocomplete;
    let pickupMarker = null,
        dropoffMarker = null;
    let currentBookingId = null;

    function initBookingInterface() {
        const center = {
            lat: -1.2921,
            lng: 36.8219
        };

        map = new google.maps.Map(document.getElementById("bookingMap"), {
            zoom: 12,
            center: center,
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
                    featureType: "road",
                    elementType: "geometry.stroke",
                    stylers: [{
                        color: "#212b23"
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

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true, // Let custom pin markers handle it
            polylineOptions: {
                strokeColor: "#d4af37",
                strokeWeight: 5
            }
        });

        // Initialize Places Autocomplete
        pickupAutocomplete = new google.maps.places.Autocomplete(document.getElementById("pickupInput"));
        dropoffAutocomplete = new google.maps.places.Autocomplete(document.getElementById("dropoffInput"));

        pickupAutocomplete.addListener("place_changed", () => {
            const place = pickupAutocomplete.getPlace();
            if (place.geometry) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                document.getElementById("p_lat").value = lat;
                document.getElementById("p_lng").value = lng;

                if (pickupMarker) pickupMarker.setMap(null);
                pickupMarker = new google.maps.Marker({
                    position: {
                        lat: lat,
                        lng: lng
                    },
                    map: map,
                    title: "Pickup",
                    icon: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
                });

                checkAndCalculateRoute();
            }
        });

        dropoffAutocomplete.addListener("place_changed", () => {
            const place = dropoffAutocomplete.getPlace();
            if (place.geometry) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                document.getElementById("d_lat").value = lat;
                document.getElementById("d_lng").value = lng;

                if (dropoffMarker) dropoffMarker.setMap(null);
                dropoffMarker = new google.maps.Marker({
                    position: {
                        lat: lat,
                        lng: lng
                    },
                    map: map,
                    title: "Destination",
                    icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });

                checkAndCalculateRoute();
            }
        });

        function resolvePinnedAddress(lat, lng) {
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
                    if (data.status === "success" && data.result.address) {
                        return data.result.address;
                    }
                    return null;
                })
                .catch(() => null);
        }

        // Click-to-Pin logic on map
        map.addListener("click", (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            const pinMode = document.querySelector('input[name="pin_mode"]:checked').value;

            if (pinMode === 'pickup') {
                document.getElementById("p_lat").value = lat;
                document.getElementById("p_lng").value = lng;
                document.getElementById("pickupInput").value = "Pinned Location (" + lat.toFixed(5) + ", " + lng.toFixed(5) + ")";

                if (pickupMarker) pickupMarker.setMap(null);
                pickupMarker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: "Pickup",
                    icon: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
                });

                resolvePinnedAddress(lat, lng).then(addr => {
                    if (addr) document.getElementById("pickupInput").value = addr;
                });

                checkAndCalculateRoute();
            } else {
                document.getElementById("d_lat").value = lat;
                document.getElementById("d_lng").value = lng;
                document.getElementById("dropoffInput").value = "Pinned Location (" + lat.toFixed(5) + ", " + lng.toFixed(5) + ")";

                if (dropoffMarker) dropoffMarker.setMap(null);
                dropoffMarker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: "Destination",
                    icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });

                resolvePinnedAddress(lat, lng).then(addr => {
                    if (addr) document.getElementById("dropoffInput").value = addr;
                });

                checkAndCalculateRoute();
            }
        });

        document.getElementById("vehicleSelect").addEventListener("change", fetchDynamicQuote);
        document.getElementById("driverSelect").addEventListener("change", fetchDynamicQuote);
    }

    function checkAndCalculateRoute() {
        const p_lat = document.getElementById("p_lat").value;
        const p_lng = document.getElementById("p_lng").value;
        const d_lat = document.getElementById("d_lat").value;
        const d_lng = document.getElementById("d_lng").value;

        if (p_lat && p_lng && d_lat && d_lng) {
            const origin = new google.maps.LatLng(parseFloat(p_lat), parseFloat(p_lng));
            const destination = new google.maps.LatLng(parseFloat(d_lat), parseFloat(d_lng));

            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING
            }, (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    fetchDynamicQuote();
                } else {
                    console.error("Directions request failed due to " + status);
                    fetchDynamicQuote();
                }
            });
        }
    }

    function fetchDynamicQuote() {
        const vehicle = document.getElementById("vehicleSelect").value;
        const driver = document.getElementById("driverSelect").value;
        const p_lat = document.getElementById("p_lat").value;
        const p_lng = document.getElementById("p_lng").value;
        const d_lat = document.getElementById("d_lat").value;
        const d_lng = document.getElementById("d_lng").value;

        if (!vehicle || !driver || !p_lat || !d_lat) {
            return;
        }

        const formData = new FormData();
        formData.append("vehicle_id", vehicle);
        formData.append("driver_id", driver);
        formData.append("pickup_latitude", p_lat);
        formData.append("pickup_longitude", p_lng);
        formData.append("dropoff_latitude", d_lat);
        formData.append("dropoff_longitude", d_lng);
        formData.append("csrf_test_name", window.getCSRFToken());

        fetch("<?= url_to('trips.quote.calculate') ?>", {
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
                    document.getElementById("summaryDistance").innerText = data.result.distance_km + " Km";
                    document.getElementById("summaryBase").innerText = "$" + data.result.base_booking_fee.toFixed(2);
                    document.getElementById("summaryFuel").innerText = "$" + data.result.per_km_fuel_cost.toFixed(2);
                    document.getElementById("summaryMaint").innerText = "$" + data.result.maintenance_reserve.toFixed(2);
                    document.getElementById("summaryDriver").innerText = "$" + data.result.driver_allowance.toFixed(2);
                    document.getElementById("summaryTotal").innerText = "$" + data.result.total_price.toFixed(2);

                    document.getElementById("priceSummary").style.display = "block";
                    document.getElementById("bookBtn").removeAttribute("disabled");
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error("Calculations failed", err));
    }

    document.getElementById("bookBtn").addEventListener("click", function() {
        const totalText = document.getElementById("summaryTotal").innerText;
        document.getElementById("modalTotal").innerText = totalText;

        const paymentModal = new bootstrap.Modal(document.getElementById("paymentModal"));
        paymentModal.show();
    });

    document.getElementById("payBtn").addEventListener("click", function() {
        const provider = document.getElementById("providerSelect").value;
        const quoteForm = document.getElementById("quoteForm");

        const alertDiv = document.getElementById("paymentAlert");
        alertDiv.innerText = "Initializing payment request...";
        alertDiv.className = "alert alert-info py-2 small";
        alertDiv.style.display = "block";

        const formData = new FormData(quoteForm);
        formData.append("provider", provider);
        formData.append("csrf_test_name", window.getCSRFToken());

        fetch("<?= base_url('payments/checkout') ?>", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                window.updateCSRFToken(data.csrf_token);
                if (data.status === "success" && data.result.authorization_url) {
                    alertDiv.innerText = "Redirecting to complete authentication...";
                    window.location.href = data.result.authorization_url;
                } else {
                    alertDiv.className = "alert alert-danger py-2 small";
                    alertDiv.innerText = "Error: " + data.message;
                }
            })
            .catch(err => {
                alertDiv.className = "alert alert-danger py-2 small";
                alertDiv.innerText = "Connection failed.";
                console.error(err);
            });
    });
</script>
<?= $this->endSection() ?>