<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Map Column -->
    <div class="col-lg-7 mb-4">
        <div class="card blueprint-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0 text-accent">Safari Route Map</h5>
                <span class="text-muted small">Click the map directly to drop pin markers.</span>
            </div>

            <div class="p-3 mb-3 rounded border bg-body-tertiary">
                <span class="text-muted small d-block mb-1">Map Pin Placement Mode:</span>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pin_mode" id="pinPickup" value="pickup" checked>
                        <label class="form-check-label small" for="pinPickup">Pin Pickup Location</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pin_mode" id="pinDropoff" value="dropoff">
                        <label class="form-check-label small" for="pinDropoff">Pin Destination</label>
                    </div>
                </div>
            </div>

            <div id="bookingMap" style="min-height: 440px; border-radius: 12px; background-color: #f0f0f0;"></div>
        </div>
    </div>

    <!-- Booking Form Column -->
    <div class="col-lg-5 mb-4">
        <div class="card blueprint-card p-4 h-100">
            <h4 class="fw-bold text-accent mb-1">Manual Booking (Admin Override)</h4>
            <p class="text-muted small mb-4">Select customer, route, and pricing. Values auto-calculate and can be overridden.</p>

            <form id="manualBookingForm" autocomplete="off" action="<?= url_to('trips.manager.manual_booking.create') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-floating mb-3">
                    <select class="form-select" name="customer_id" required>
                        <option value="" selected disabled>Select Customer...</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['first_name'] . ' ' . $c['last_name']) ?> (<?= esc($c['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <label>Customer</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="pickupInput" name="pickup_address" placeholder="Pickup Address" required>
                    <label for="pickupInput">Pickup Location</label>
                    <input type="hidden" id="p_lat" name="pickup_latitude">
                    <input type="hidden" id="p_lng" name="pickup_longitude">
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="dropoffInput" name="dropoff_address" placeholder="Dropoff Address" required>
                    <label for="dropoffInput">Destination</label>
                    <input type="hidden" id="d_lat" name="dropoff_latitude">
                    <input type="hidden" id="d_lng" name="dropoff_longitude">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="vehicleSelect" name="vehicle_id" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php foreach ($vehicles as $v): ?>
                                    <option value="<?= $v->id ?>"><?= esc($v->model) ?> (<?= esc($v->plate_number) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <label>Vehicle</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="driverSelect" name="driver_id" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php foreach ($drivers as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= esc($d['first_name'] . ' ' . $d['last_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Driver</label>
                        </div>
                    </div>
                </div>

                <div id="priceSummary" class="p-3 mb-3 rounded bg-success bg-opacity-10 border border-success" style="display: none;">
                    <h6 class="fw-bold text-accent mb-3">Auto-Calculated Cost</h6>
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
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Price:</span>
                        <span class="fs-4 fw-bold text-accent" id="summaryTotal">$0.00</span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" class="form-control" name="distance_km" id="distanceInput" placeholder="System calculated value">
                            <label>Distance (Km) - Override</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" class="form-control" name="total_price" id="priceInput" placeholder="System calculated value">
                            <label>Total Price ($) - Override</label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" name="payment_status" required>
                                <option value="pending">Pending (Awaiting Payment)</option>
                                <option value="paid">Paid (Immediate Confirmation)</option>
                                <option value="manual_verified">Manual Verified</option>
                            </select>
                            <label>Payment Status</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="paystack_reference" placeholder="Manual-REF">
                            <label>Reference (optional)</label>
                        </div>
                    </div>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit" id="createBtn" disabled>Create Booking</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc($googleApiKey) ?>&libraries=places&callback=initBookingInterface" defer></script>
<script>
    let map, directionsService, directionsRenderer;
    let pickupAutocomplete, dropoffAutocomplete;
    let pickupMarker = null,
        dropoffMarker = null;

    function initBookingInterface() {
        const center = {
            lat: -1.2921,
            lng: 36.8219
        };

        map = new google.maps.Map(document.getElementById("bookingMap"), {
            zoom: 12,
            center: center
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#0d6efd",
                strokeWeight: 5
            }
        });

        pickupAutocomplete = new google.maps.places.Autocomplete(document.getElementById("pickupInput"));
        dropoffAutocomplete = new google.maps.places.Autocomplete(document.getElementById("dropoffInput"));

        pickupAutocomplete.addListener("place_changed", () => {
            const place = pickupAutocomplete.getPlace();
            if (place.geometry) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                document.getElementById("p_lat").value = lat;
                document.getElementById("p_lng").value = lng;
                const address = place.formatted_address || place.name || "";
                document.getElementById("pickupInput").value = address;
                if (pickupMarker) pickupMarker.setMap(null);
                pickupMarker = new google.maps.Marker({
                    position: {
                        lat,
                        lng
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
                const address = place.formatted_address || place.name || "";
                document.getElementById("dropoffInput").value = address;
                if (dropoffMarker) dropoffMarker.setMap(null);
                dropoffMarker = new google.maps.Marker({
                    position: {
                        lat,
                        lng
                    },
                    map: map,
                    title: "Destination",
                    icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
                checkAndCalculateRoute();
            }
        });

        map.addListener("click", (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            const pinMode = document.querySelector('input[name="pin_mode"]:checked').value;

            if (pinMode === 'pickup') {
                document.getElementById("p_lat").value = lat;
                document.getElementById("p_lng").value = lng;
                const tempText = "Pinned Location (" + lat.toFixed(5) + ", " + lng.toFixed(5) + ")";
                document.getElementById("pickupInput").value = tempText;
                if (pickupMarker) pickupMarker.setMap(null);
                pickupMarker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: "Pickup",
                    icon: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
                });
                resolveAddress("pickupInput", lat, lng, tempText);
                checkAndCalculateRoute();
            } else {
                document.getElementById("d_lat").value = lat;
                document.getElementById("d_lng").value = lng;
                const tempText = "Pinned Location (" + lat.toFixed(5) + ", " + lng.toFixed(5) + ")";
                document.getElementById("dropoffInput").value = tempText;
                if (dropoffMarker) dropoffMarker.setMap(null);
                dropoffMarker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                    title: "Destination",
                    icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
                resolveAddress("dropoffInput", lat, lng, tempText);
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

        if (!vehicle || !driver || !p_lat || !d_lat) return;

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
                    document.getElementById("distanceInput").value = data.result.distance_km;
                    document.getElementById("priceInput").value = data.result.total_price.toFixed(2);
                    document.getElementById("createBtn").removeAttribute("disabled");
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error("Calculations failed", err));
    }

    function resolveAddress(inputId, lat, lng, fallbackText) {
        const formData = new FormData();
        formData.append("latitude", lat);
        formData.append("longitude", lng);
        formData.append("csrf_test_name", window.getCSRFToken());
        fetch("<?= url_to('trips.geocode.reverse') ?>", {
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
                    document.getElementById(inputId).value = data.result.address;
                } else {
                    document.getElementById(inputId).value = fallbackText;
                }
            })
            .catch(() => {
                document.getElementById(inputId).value = fallbackText;
            });
    }

    document.getElementById("manualBookingForm").addEventListener("submit", function(e) {
        const customer = this.querySelector('select[name="customer_id"]').value;
        const vehicle = document.getElementById("vehicleSelect").value;
        const driver = document.getElementById("driverSelect").value;
        if (!customer || !vehicle || !driver) {
            e.preventDefault();
            alert("Please select a customer, vehicle, and driver before submitting.");
        }
    });
</script>
<?= $this->endSection() ?>