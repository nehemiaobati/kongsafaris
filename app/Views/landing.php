<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    /* Premium Landing Page Styles */
    .hero-carousel-item {
        height: 85vh;
        min-height: 500px;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
    }

    .hero-carousel-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(18, 24, 19, 0.4) 0%, rgba(18, 24, 19, 0.85) 100%);
    }

    .carousel-caption-custom {
        position: absolute;
        bottom: 15%;
        left: 5%;
        right: 5%;
        z-index: 10;
        max-width: 700px;
    }

    .trust-bar {
        background: rgba(30, 47, 32, 0.15);
        border-top: 1px solid rgba(212, 175, 55, 0.15);
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        backdrop-filter: blur(10px);
    }

    .gradient-icon-box {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--safari-accent) 0%, #a6851e 100%);
        color: #121813;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
    }

    /* Fleet Gallery Hover Effects */
    .fleet-card {
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: var(--safari-card-bg);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .fleet-img-container {
        position: relative;
        overflow: hidden;
        aspect-ratio: 16/10;
    }

    .fleet-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .fleet-card:hover {
        transform: translateY(-8px);
        border-color: rgba(212, 175, 55, 0.35);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
    }

    .fleet-card:hover .fleet-img-container img {
        transform: scale(1.08);
    }

    .fleet-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(18, 24, 19, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .fleet-card:hover .fleet-overlay {
        opacity: 1;
    }

    /* Destination Cards */
    .dest-card {
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 4/5;
        border: none;
    }

    .dest-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .dest-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(18, 24, 19, 0.9) 0%, rgba(18, 24, 19, 0.2) 60%, transparent 100%);
    }

    .dest-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        z-index: 10;
    }

    .dest-card:hover img {
        transform: scale(1.05);
    }

    /* How It Works steps */
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: rgba(212, 175, 55, 0.1);
        border: 2px solid var(--safari-accent);
        color: var(--safari-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        margin: 0 auto 1rem auto;
    }

    /* Scroll Reveal Animations */
    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s cubic-bezier(0.165, 0.84, 0.44, 1), transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .reveal-on-scroll.revealed {
        opacity: 1;
        transform: translateY(0);
    }

    /* Carousel Custom Controls */
    .carousel-indicators [data-bs-target] {
        background-color: var(--safari-accent);
    }

    .accordion-button {
        background-color: rgba(30, 47, 32, 0.15) !important;
        color: #f1f3f2 !important;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(30, 47, 32, 0.4) !important;
        color: var(--safari-accent) !important;
        box-shadow: none;
    }

    .accordion-item {
        background-color: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 12px !important;
        overflow: hidden;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

</div> <!-- Close default container to allow full-width hero carousel -->

<!-- Hero Carousel -->
<div id="heroCarousel" class="carousel slide carousel-fade mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active hero-carousel-item" style="background-image: url('<?= base_url('assets/img/safari-hero.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 text-uppercase fw-bold">Wild Kenya Adventures</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Safari Car Hire <br><span class="text-accent">Reimagined</span></h1>
                    <p class="lead text-light mb-4">Book robust 4×4 Land Cruisers and Safari vans. Live trip coordinates streaming, experienced local drivers, and secure M-Pesa integrations.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Start Booking</a>
                        <a href="#fleet" class="btn btn-outline-light btn-lg px-4">Explore Fleet</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item hero-carousel-item" style="background-image: url('<?= base_url('assets/img/fleet-land-cruiser.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 text-uppercase fw-bold">Built For the Bush</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Explore the Wild <br><span class="text-accent">Without Limits</span></h1>
                    <p class="lead text-light mb-4">Traverse rugged river crossings and rolling savannahs in custom-built 4×4s designed to handle Kenya's wilderness challenges.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Book 4x4 Cruiser</a>
                        <a href="#destinations" class="btn btn-outline-light btn-lg px-4">Top Destinations</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item hero-carousel-item" style="background-image: url('<?= base_url('assets/img/fleet-safari-van.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 text-uppercase fw-bold">Family & Group Travel</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Comfort on <br><span class="text-accent">Every Journey</span></h1>
                    <p class="lead text-light mb-4">Spacious safari vans equipped with pop-up roofs for optimal game viewing and high-frequency communication rigs.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Book Safari Van</a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">How It Works</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Trust Bar -->
<div class="trust-bar py-4 mb-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="fs-3 text-accent">🤠</span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold text-white">Verified Drivers</h6>
                        <small class="text-muted">Licensed & experienced</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="fs-3 text-accent">📍</span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold text-white">Live GPS Tracking</h6>
                        <small class="text-muted">Real-time status updates</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="fs-3 text-accent">💵</span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold text-white">Dynamic Pricing</h6>
                        <small class="text-muted">No hidden commissions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="fs-3 text-accent">📱</span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold text-white">M-Pesa Supported</h6>
                        <small class="text-muted">Easy local payments</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container"> <!-- Re-open container for page body -->

<!-- Value Propositions -->
<div class="row py-5 mb-5 align-items-center reveal-on-scroll">
    <div class="col-lg-5 mb-5 mb-lg-0">
        <span class="text-accent fw-bold text-uppercase tracking-wider">Why Choose Us</span>
        <h2 class="display-5 fw-bold text-white mt-2 mb-4">Complete Control Over Your Safari Journey</h2>
        <p class="text-muted mb-4">Kong Safaris isn't just a transport hire business. We are a technology-driven travel partner that guarantees transparency, safety, and a premium booking experience from start to finish.</p>
        <div class="d-flex gap-3">
            <a href="<?= url_to('auth.register') ?>" class="btn btn-primary px-4 py-2">Get Started Now</a>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="row g-4">
            <div class="col-sm-6">
                <div class="card blueprint-card p-4 h-100">
                    <div class="gradient-icon-box">📊</div>
                    <h5 class="fw-bold text-white">Dynamic Quoting</h5>
                    <p class="text-muted small mb-0">Algorithms calculate fares based on actual route mileage, fuel price, driver allowance, and park-entry metrics.</p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card blueprint-card p-4 h-100">
                    <div class="gradient-icon-box">🧭</div>
                    <h5 class="fw-bold text-white">Live Tracking</h5>
                    <p class="text-muted small mb-0">Follow your vehicle on Google Maps as your driver navigates the savanna, complete with coordinate streams.</p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card blueprint-card p-4 h-100">
                    <div class="gradient-icon-box">🛡️</div>
                    <h5 class="fw-bold text-white">Operations Dashboard</h5>
                    <p class="text-muted small mb-0">For fleet owners: live operations portal to coordinate runs, update vehicle status, and track driver assignments.</p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card blueprint-card p-4 h-100">
                    <div class="gradient-icon-box">💳</div>
                    <h5 class="fw-bold text-white">Integrated Billing</h5>
                    <p class="text-muted small mb-0">Instant payments via M-Pesa, Card, and Airtel Money with automatic receipts and accounting updates.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Counter -->
<div class="row py-5 mb-5 text-center reveal-on-scroll bg-body-tertiary rounded-4 p-4">
    <div class="col-md-3 col-6 my-3">
        <h2 class="display-4 fw-bold text-accent mb-0"><span class="stat-number" data-target="1500">0</span>+</h2>
        <p class="text-muted mb-0">Safaris Completed</p>
    </div>
    <div class="col-md-3 col-6 my-3">
        <h2 class="display-4 fw-bold text-accent mb-0"><span class="stat-number" data-target="45">0</span>+</h2>
        <p class="text-muted mb-0">Modern Vehicles</p>
    </div>
    <div class="col-md-3 col-6 my-3">
        <h2 class="display-4 fw-bold text-accent mb-0"><span class="stat-number" data-target="32">0</span>+</h2>
        <p class="text-muted mb-0">Professional Drivers</p>
    </div>
    <div class="col-md-3 col-6 my-3">
        <h2 class="display-4 fw-bold text-accent mb-0"><span class="stat-number" data-target="99">0</span>%</h2>
        <p class="text-muted mb-0">Satisfaction Rate</p>
    </div>
</div>

<!-- Fleet Showcase Gallery -->
<div id="fleet" class="py-5 mb-5 reveal-on-scroll">
    <div class="text-center mb-5">
        <span class="text-accent fw-bold text-uppercase">Our Fleet</span>
        <h2 class="display-5 fw-bold text-white mt-1">Vetted Vehicles for African Terrain</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Every vehicle in our safari fleet is strictly maintained, fully insured, and customized with pop-up hatches for wildlife observation.</p>
    </div>
    <div class="row g-4">
        <!-- Land Cruiser -->
        <div class="col-lg-4 col-md-6">
            <div class="card fleet-card h-100">
                <div class="fleet-img-container">
                    <img src="<?= base_url('assets/img/fleet-land-cruiser.png') ?>" alt="Toyota Land Cruiser 4x4">
                    <div class="fleet-overlay">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="fw-bold text-white mb-0">Safari Land Cruiser</h4>
                        <span class="badge bg-warning text-dark fw-bold">4×4 Offroad</span>
                    </div>
                    <p class="text-muted small">The industry standard for African safaris. Seats up to 7 passengers, featuring high-clearance offroad suspension, dual fuel tanks, and custom pop-up roof.</p>
                    <hr class="border-secondary my-3">
                    <div class="row text-center text-muted small">
                        <div class="col-4 border-end border-secondary">👥 7 Seats</div>
                        <div class="col-4 border-end border-secondary">🎒 Large</div>
                        <div class="col-4">⚡ Diesel</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Safari Van -->
        <div class="col-lg-4 col-md-6">
            <div class="card fleet-card h-100">
                <div class="fleet-img-container">
                    <img src="<?= base_url('assets/img/fleet-safari-van.png') ?>" alt="Safari Tour Van">
                    <div class="fleet-overlay">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="fw-bold text-white mb-0">Safari Minivan</h4>
                        <span class="badge bg-warning text-dark fw-bold">Group Travel</span>
                    </div>
                    <p class="text-muted small">Perfect for group tours and families. Built with comfortable custom reclining seats, a roof hatch, and optimized charging ports for cameras and smartphones.</p>
                    <hr class="border-secondary my-3">
                    <div class="row text-center text-muted small">
                        <div class="col-4 border-end border-secondary">👥 8 Seats</div>
                        <div class="col-4 border-end border-secondary">🎒 Extra Lg</div>
                        <div class="col-4">⚡ Diesel</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Luxury SUV -->
        <div class="col-lg-4 col-md-6 mx-auto">
            <div class="card fleet-card h-100">
                <div class="fleet-img-container">
                    <img src="<?= base_url('assets/img/fleet-luxury-suv.png') ?>" alt="Luxury Safari SUV">
                    <div class="fleet-overlay">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="fw-bold text-white mb-0">Luxury Safari SUV</h4>
                        <span class="badge bg-warning text-dark fw-bold">Executive</span>
                    </div>
                    <p class="text-muted small">Premium execution for executive VIP transfers or high-end lodge itineraries. Leather interiors, climate control, and unmatched ride comfort on rough gravel routes.</p>
                    <hr class="border-secondary my-3">
                    <div class="row text-center text-muted small">
                        <div class="col-4 border-end border-secondary">👥 5 Seats</div>
                        <div class="col-4 border-end border-secondary">🎒 Medium</div>
                        <div class="col-4">⚡ Petrol</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div id="how-it-works" class="py-5 mb-5 reveal-on-scroll bg-body-tertiary rounded-4 p-5">
    <div class="text-center mb-5">
        <span class="text-accent fw-bold text-uppercase">How It Works</span>
        <h2 class="display-5 fw-bold text-white mt-1">Book Your Safari in 3 Easy Steps</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Our platform streamlines booking, pricing updates, and coordination so you can focus on the adventure.</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="step-number">1</div>
            <h5 class="fw-bold text-white">Choose Your Route & Vehicle</h5>
            <p class="text-muted small">Select your pickup, destinations, and vehicle type. Our system will generate a detailed dynamic quote immediately.</p>
        </div>
        <div class="col-md-4">
            <div class="step-number">2</div>
            <h5 class="fw-bold text-white">Instant Secure Payment</h5>
            <p class="text-muted small">Confirm booking using Paystack options including M-Pesa, Card or Bank Transfer. Fares are secured directly.</p>
        </div>
        <div class="col-md-4">
            <div class="step-number">3</div>
            <h5 class="fw-bold text-white">Track & Enjoy the Ride</h5>
            <p class="text-muted small">Receive driver information, trace location updates live via client dashboard, and coordinate with operations team.</p>
        </div>
    </div>
</div>

<!-- Popular Destinations -->
<div id="destinations" class="py-5 mb-5 reveal-on-scroll">
    <div class="text-center mb-5">
        <span class="text-accent fw-bold text-uppercase">Popular Routes</span>
        <h2 class="display-5 fw-bold text-white mt-1">Top Safari Destinations in Kenya</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Direct routes served with automated quotes, flat fuel averages, and pre-negotiated driver accommodation rates.</p>
    </div>
    <div class="row g-4">
        <!-- Maasai Mara -->
        <div class="col-lg-3 col-sm-6">
            <div class="card dest-card">
                <img src="<?= base_url('assets/img/safari-hero.png') ?>" alt="Maasai Mara National Reserve">
                <div class="dest-content">
                    <h5 class="fw-bold text-white mb-0">Maasai Mara</h5>
                    <p class="text-muted small mb-0">Savanna wilderness & Wildebeest Migration</p>
                </div>
            </div>
        </div>
        <!-- Amboseli -->
        <div class="col-lg-3 col-sm-6">
            <div class="card dest-card">
                <img src="<?= base_url('assets/img/fleet-safari-van.png') ?>" alt="Amboseli National Park">
                <div class="dest-content">
                    <h5 class="fw-bold text-white mb-0">Amboseli</h5>
                    <p class="text-muted small mb-0">Elephants and Mount Kilimanjaro views</p>
                </div>
            </div>
        </div>
        <!-- Lake Nakuru -->
        <div class="col-lg-3 col-sm-6">
            <div class="card dest-card">
                <img src="<?= base_url('assets/img/fleet-luxury-suv.png') ?>" alt="Lake Nakuru">
                <div class="dest-content">
                    <h5 class="fw-bold text-white mb-0">Lake Nakuru</h5>
                    <p class="text-muted small mb-0">Flamingos, rhinos and scenic viewpoints</p>
                </div>
            </div>
        </div>
        <!-- Tsavo -->
        <div class="col-lg-3 col-sm-6">
            <div class="card dest-card">
                <img src="<?= base_url('assets/img/fleet-land-cruiser.png') ?>" alt="Tsavo National Park">
                <div class="dest-content">
                    <h5 class="fw-bold text-white mb-0">Tsavo East & West</h5>
                    <p class="text-muted small mb-0">Red dust elephants and lava landscapes</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials -->
<div class="py-5 mb-5 reveal-on-scroll text-center bg-body-tertiary rounded-4 p-5">
    <span class="text-accent fw-bold text-uppercase">Reviews</span>
    <h2 class="display-5 fw-bold text-white mt-1 mb-5">What Tour Operators Say</h2>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner" style="max-width: 800px; margin: 0 auto;">
            <!-- Review 1 -->
            <div class="carousel-item active">
                <span class="fs-1 text-accent">“</span>
                <p class="fs-5 text-light mb-4">"Kong Safaris completely overhauled our transport logistics. The live tracking gives our clients peace of mind, and the auto-generated quotes saved us countless manual negotiation hours."</p>
                <h6 class="fw-bold text-white">David Kiprop</h6>
                <small class="text-muted">Operations Director, Rift Valley Safaris</small>
            </div>
            <!-- Review 2 -->
            <div class="carousel-item">
                <span class="fs-1 text-accent">“</span>
                <p class="fs-5 text-light mb-4">"The M-Pesa integration makes booking confirmation instantaneous. We can deploy a Land Cruiser to Maasai Mara on short notice knowing that payment is confirmed through Paystack."</p>
                <h6 class="fw-bold text-white">Sarah Jenkins</h6>
                <small class="text-muted">Founder, Savannah Escapes Ltd</small>
            </div>
            <!-- Review 3 -->
            <div class="carousel-item">
                <span class="fs-1 text-accent">“</span>
                <p class="fs-5 text-light mb-4">"Having a mobile-ready dashboard for drivers allows us to know exactly when trips start, current vehicle location coordinates, and when the safari concludes safely."</p>
                <h6 class="fw-bold text-white">Moses Omondi</h6>
                <small class="text-muted">Transport Manager, East African Tours</small>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<!-- FAQ Accordion -->
<div id="faq" class="py-5 mb-5 reveal-on-scroll">
    <div class="text-center mb-5">
        <span class="text-accent fw-bold text-uppercase">FAQ</span>
        <h2 class="display-5 fw-bold text-white mt-1">Frequently Asked Questions</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Everything you need to know about our fleet booking and operations engine.</p>
    </div>
    <div class="accordion accordion-flush" id="faqAccordion" style="max-width: 800px; margin: 0 auto;">
        <!-- Q1 -->
        <div class="accordion-item mb-3 bg-transparent">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1">
                    How is the dynamic safari trip price calculated?
                </button>
            </h2>
            <div id="faq-1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Fares are automatically generated based on the actual driving distance, estimated fuel consumption for the chosen vehicle category, driver daily allowances (including park accommodation rules), and a vehicle maintenance reserve.
                </div>
            </div>
        </div>
        <!-- Q2 -->
        <div class="accordion-item mb-3 bg-transparent">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2">
                    Does the booking include a driver and fuel?
                </button>
            </h2>
            <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Yes! All standard rentals on Kong Safaris are fully chauffeured by our licensed professional guides and include base fuel allocations calculated for your pre-specified route.
                </div>
            </div>
        </div>
        <!-- Q3 -->
        <div class="accordion-item mb-3 bg-transparent">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3">
                    Can I track my booked vehicle in real time?
                </button>
            </h2>
            <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Absolutely. Once a trip commences, drivers stream coordinates from their mobile workspace, allowing clients and managers to visualize the vehicle's progress on an interactive map.
                </div>
            </div>
        </div>
        <!-- Q4 -->
        <div class="accordion-item mb-3 bg-transparent">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4">
                    What payment methods do you support?
                </button>
            </h2>
            <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    We support secure online checkout via Paystack, accommodating M-Pesa, Card payments (Visa, Mastercard), and Airtel Money.
                </div>
            </div>
        </div>
        <!-- Q5 -->
        <div class="accordion-item mb-3 bg-transparent">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-5">
                    What happens if a vehicle breaks down during safari?
                </button>
            </h2>
            <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    We maintain standby backup vehicles in Nairobi and Narok (near Maasai Mara). If any mechanical issue occurs, our operations team can quickly deploy replacement options.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Banner -->
<div class="py-5 mb-5 reveal-on-scroll bg-gradient text-center rounded-4 p-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--safari-primary) 0%, #0d120e 100%); border: 1px solid rgba(212, 175, 55, 0.25);">
    <div class="position-relative z-3">
        <h2 class="display-5 fw-bold text-white mb-3">Begin Your Adventure Today</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">Create your operator account to customize booking coordinates, view historical trip routes, and generate quotes in seconds.</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-5">Register Account</a>
            <a href="<?= url_to('auth.login') ?>" class="btn btn-outline-light btn-lg px-4">Sign In</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // 2. IntersectionObserver for scroll reveal animations
        const revealElements = document.querySelectorAll('.reveal-on-scroll');
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    // Stop observing once revealed
                    observer.unobserve(entry.target);
                    
                    // If this element contains counter numbers, trigger them
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => triggerCounter(counter));
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));

        // 3. Counter Animation Function
        function triggerCounter(counter) {
            const target = parseInt(counter.getAttribute('data-target'), 10);
            const duration = 1500; // ms
            const stepTime = 15; // ms
            const steps = duration / stepTime;
            const stepVal = target / steps;
            let current = 0;

            const timer = setInterval(() => {
                current += stepVal;
                if (current >= target) {
                    counter.innerText = target;
                    clearInterval(timer);
                } else {
                    counter.innerText = Math.floor(current);
                }
            }, stepTime);
        }
    });
</script>
<?= $this->endSection() ?>