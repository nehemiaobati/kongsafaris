<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
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
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.65) 100%);
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
        background: rgba(0, 0, 0, 0.02);
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .gradient-icon-box {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--safari-accent) 0%, #0b5ed7 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
    }

    .fleet-card {
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #ffffff;
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
        border-color: var(--safari-accent);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
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
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .fleet-card:hover .fleet-overlay {
        opacity: 1;
    }

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
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.2) 60%, transparent 100%);
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

    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.08);
        border: 2px solid var(--safari-accent);
        color: var(--safari-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        margin: 0 auto 1rem auto;
    }

    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s cubic-bezier(0.165, 0.84, 0.44, 1), transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .reveal-on-scroll.revealed {
        opacity: 1;
        transform: translateY(0);
    }

    .carousel-indicators [data-bs-target] {
        background-color: var(--safari-accent);
    }

    .accordion-button {
        background-color: rgba(0, 0, 0, 0.02) !important;
        color: #333 !important;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.05) !important;
        color: var(--safari-accent) !important;
        box-shadow: none;
    }

    .accordion-item {
        background-color: transparent !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        border-radius: 12px !important;
        overflow: hidden;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

</div>

<div id="heroCarousel" class="carousel slide carousel-fade mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active hero-carousel-item" style="background-image: url('<?= base_url('assets/img/safari-hero.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-primary text-white mb-3 px-3 py-2 text-uppercase fw-bold">Wild Kenya Adventures</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Safari Car Hire <br><span class="text-white">Reimagined</span></h1>
                    <p class="lead text-white mb-4">Book robust 4x4 Land Cruisers and Safari vans. Live trip coordinates streaming, experienced local drivers, and secure M-Pesa integrations.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Start Booking</a>
                        <a href="#fleet" class="btn btn-outline-light btn-lg px-4">Explore Fleet</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item hero-carousel-item" style="background-image: url('<?= base_url('assets/img/fleet-land-cruiser.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-primary text-white mb-3 px-3 py-2 text-uppercase fw-bold">Built For the Bush</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Explore the Wild <br><span class="text-white">Without Limits</span></h1>
                    <p class="lead text-white mb-4">Traverse rugged river crossings and rolling savannahs in custom-built 4x4s designed to handle Kenya's wilderness challenges.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Book 4x4 Cruiser</a>
                        <a href="#destinations" class="btn btn-outline-light btn-lg px-4">Top Destinations</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item hero-carousel-item" style="background-image: url('<?= base_url('assets/img/fleet-safari-van.png') ?>');">
            <div class="container h-100 position-relative">
                <div class="carousel-caption-custom text-start">
                    <span class="badge bg-primary text-white mb-3 px-3 py-2 text-uppercase fw-bold">Family & Group Travel</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Comfort on <br><span class="text-white">Every Journey</span></h1>
                    <p class="lead text-white mb-4">Spacious safari vans equipped with pop-up roofs for optimal game viewing and high-frequency communication rigs.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-lg px-4">Book Safari Van</a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">How It Works</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="trust-bar py-4 mb-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="gradient-icon-box" style="width: 48px; height: 48px; font-size: 1.25rem; margin-bottom: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Verified Drivers</h6>
                        <small class="text-muted">Licensed & experienced</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="gradient-icon-box" style="width: 48px; height: 48px; font-size: 1.25rem; margin-bottom: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Live GPS Tracking</h6>
                        <small class="text-muted">Real-time status updates</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="gradient-icon-box" style="width: 48px; height: 48px; font-size: 1.25rem; margin-bottom: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Dynamic Pricing</h6>
                        <small class="text-muted">No hidden commissions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="gradient-icon-box" style="width: 48px; height: 48px; font-size: 1.25rem; margin-bottom: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="6" width="20" height="12" rx="2" />
                            <path d="M6 12h4" />
                            <path d="M14 12h4" />
                        </svg>
                    </span>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">M-Pesa Supported</h6>
                        <small class="text-muted">Easy local payments</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">

    <!-- Value Propositions -->
    <div class="row py-5 mb-5 align-items-center reveal-on-scroll">
        <div class="col-lg-5 mb-5 mb-lg-0">
            <span class="text-accent fw-bold text-uppercase tracking-wider">Why Choose Us</span>
            <h2 class="display-5 fw-bold mt-2 mb-4">Complete Control Over Your Safari Journey</h2>
            <p class="text-muted mb-4">Kong Safaris isn't just a transport hire business. We are a technology-driven travel partner that guarantees transparency, safety, and a premium booking experience from start to finish.</p>
            <div class="d-flex gap-3">
                <a href="<?= url_to('auth.register') ?>" class="btn btn-primary px-4 py-2">Get Started Now</a>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-4">
                <div class="col-sm-6">
                    <div class="card blueprint-card p-4 h-100">
                        <div class="gradient-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="15" x2="4" y2="21" />
                                <line x1="12" y1="9" x2="12" y2="21" />
                                <line x1="20" y1="3" x2="20" y2="21" />
                            </svg>
                        </div>
                        <h5 class="fw-bold">Dynamic Quoting</h5>
                        <p class="text-muted small mb-0">Algorithms calculate fares based on actual route mileage, fuel price, driver allowance, and park-entry metrics.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card blueprint-card p-4 h-100">
                        <div class="gradient-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" />
                            </svg>
                        </div>
                        <h5 class="fw-bold">Live Tracking</h5>
                        <p class="text-muted small mb-0">Follow your vehicle on Google Maps as your driver navigates the savanna, complete with coordinate streams.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card blueprint-card p-4 h-100">
                        <div class="gradient-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <h5 class="fw-bold">Operations Dashboard</h5>
                        <p class="text-muted small mb-0">For fleet owners: live operations portal to coordinate runs, update vehicle status, and track driver assignments.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card blueprint-card p-4 h-100">
                        <div class="gradient-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                <line x1="1" y1="10" x2="23" y2="10" />
                            </svg>
                        </div>
                        <h5 class="fw-bold">Integrated Billing</h5>
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
            <h2 class="display-5 fw-bold mt-1">Vetted Vehicles for African Terrain</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Every vehicle in our safari fleet is strictly maintained, fully insured, and customized with pop-up hatches for wildlife observation.</p>
        </div>
        <div class="row g-4">
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
                            <h4 class="fw-bold mb-0">Safari Land Cruiser</h4>
                            <span class="badge bg-primary text-white fw-bold">4x4 Offroad</span>
                        </div>
                        <p class="text-muted small">The industry standard for African safaris. Seats up to 7 passengers, featuring high-clearance offroad suspension, dual fuel tanks, and custom pop-up roof.</p>
                        <hr class="my-3">
                        <div class="row text-center text-muted small">
                            <div class="col-4 border-end">7 Seats</div>
                            <div class="col-4 border-end">Large</div>
                            <div class="col-4">Diesel</div>
                        </div>
                    </div>
                </div>
            </div>
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
                            <h4 class="fw-bold mb-0">Safari Minivan</h4>
                            <span class="badge bg-primary text-white fw-bold">Group Travel</span>
                        </div>
                        <p class="text-muted small">Perfect for group tours and families. Built with comfortable custom reclining seats, a roof hatch, and optimized charging ports for cameras and smartphones.</p>
                        <hr class="my-3">
                        <div class="row text-center text-muted small">
                            <div class="col-4 border-end">8 Seats</div>
                            <div class="col-4 border-end">Extra Lg</div>
                            <div class="col-4">Diesel</div>
                        </div>
                    </div>
                </div>
            </div>
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
                            <h4 class="fw-bold mb-0">Luxury Safari SUV</h4>
                            <span class="badge bg-primary text-white fw-bold">Executive</span>
                        </div>
                        <p class="text-muted small">Premium execution for executive VIP transfers or high-end lodge itineraries. Leather interiors, climate control, and unmatched ride comfort on rough gravel routes.</p>
                        <hr class="my-3">
                        <div class="row text-center text-muted small">
                            <div class="col-4 border-end">5 Seats</div>
                            <div class="col-4 border-end">Medium</div>
                            <div class="col-4">Petrol</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div id="how-it-works" class="py-5 mb-5 reveal-on-scroll bg-body-tertiary rounded-4 p-5">
        <div class="text-center mb-5">
            <span class="text-accent fw-bold text-uppercase">How It Works</span>
            <h2 class="display-5 fw-bold mt-1">Book Your Safari in 3 Easy Steps</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Our platform streamlines booking, pricing updates, and coordination so you can focus on the adventure.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="step-number">1</div>
                <h5 class="fw-bold">Choose Your Route & Vehicle</h5>
                <p class="text-muted small">Select your pickup, destinations, and vehicle type. Our system will generate a detailed dynamic quote immediately.</p>
            </div>
            <div class="col-md-4">
                <div class="step-number">2</div>
                <h5 class="fw-bold">Instant Secure Payment</h5>
                <p class="text-muted small">Confirm booking using Paystack options including M-Pesa, Card or Bank Transfer. Fares are secured directly.</p>
            </div>
            <div class="col-md-4">
                <div class="step-number">3</div>
                <h5 class="fw-bold">Track & Enjoy the Ride</h5>
                <p class="text-muted small">Receive driver information, trace location updates live via client dashboard, and coordinate with operations team.</p>
            </div>
        </div>
    </div>

    <!-- Popular Destinations -->
    <div id="destinations" class="py-5 mb-5 reveal-on-scroll">
        <div class="text-center mb-5">
            <span class="text-accent fw-bold text-uppercase">Popular Routes</span>
            <h2 class="display-5 fw-bold mt-1">Top Safari Destinations in Kenya</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Direct routes served with automated quotes, flat fuel averages, and pre-negotiated driver accommodation rates.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-sm-6">
                <div class="card dest-card">
                    <img src="<?= base_url('assets/img/dest-maasai-mara.png') ?>" alt="Maasai Mara">
                    <div class="dest-content">
                        <h5 class="fw-bold text-white mb-0">Maasai Mara</h5>
                        <p class="text-white small mb-0 opacity-75">Savanna wilderness & Wildebeest Migration</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card dest-card">
                    <img src="<?= base_url('assets/img/dest-amboseli.png') ?>" alt="Amboseli">
                    <div class="dest-content">
                        <h5 class="fw-bold text-white mb-0">Amboseli</h5>
                        <p class="text-white small mb-0 opacity-75">Elephants and Mount Kilimanjaro views</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card dest-card">
                    <img src="<?= base_url('assets/img/dest-lake-nakuru.png') ?>" alt="Lake Nakuru">
                    <div class="dest-content">
                        <h5 class="fw-bold text-white mb-0">Lake Nakuru</h5>
                        <p class="text-white small mb-0 opacity-75">Flamingos, rhinos and scenic viewpoints</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card dest-card">
                    <img src="<?= base_url('assets/img/dest-tsavo.png') ?>" alt="Tsavo">
                    <div class="dest-content">
                        <h5 class="fw-bold text-white mb-0">Tsavo East & West</h5>
                        <p class="text-white small mb-0 opacity-75">Red dust elephants and lava landscapes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="py-5 mb-5 reveal-on-scroll text-center bg-body-tertiary rounded-4 p-5">
        <span class="text-accent fw-bold text-uppercase">Reviews</span>
        <h2 class="display-5 fw-bold mt-1 mb-5">What Tour Operators Say</h2>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" style="max-width: 800px; margin: 0 auto;">
                <div class="carousel-item active">
                    <span class="fs-1 text-accent">"</span>
                    <p class="fs-5 mb-4">"Kong Safaris completely overhauled our transport logistics. The live tracking gives our clients peace of mind, and the auto-generated quotes saved us countless manual negotiation hours."</p>
                    <h6 class="fw-bold">David Kiprop</h6>
                    <small class="text-muted">Operations Director, Rift Valley Safaris</small>
                </div>
                <div class="carousel-item">
                    <span class="fs-1 text-accent">"</span>
                    <p class="fs-5 mb-4">"The M-Pesa integration makes booking confirmation instantaneous. We can deploy a Land Cruiser to Maasai Mara on short notice knowing that payment is confirmed through Paystack."</p>
                    <h6 class="fw-bold">Sarah Jenkins</h6>
                    <small class="text-muted">Founder, Savannah Escapes Ltd</small>
                </div>
                <div class="carousel-item">
                    <span class="fs-1 text-accent">"</span>
                    <p class="fs-5 mb-4">"Having a mobile-ready dashboard for drivers allows us to know exactly when trips start, current vehicle location coordinates, and when the safari concludes safely."</p>
                    <h6 class="fw-bold">Moses Omondi</h6>
                    <small class="text-muted">Transport Manager, East African Tours</small>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div id="faq" class="py-5 mb-5 reveal-on-scroll">
        <div class="text-center mb-5">
            <span class="text-accent fw-bold text-uppercase">FAQ</span>
            <h2 class="display-5 fw-bold mt-1">Frequently Asked Questions</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Everything you need to know about our fleet booking and operations engine.</p>
        </div>
        <div class="accordion accordion-flush" id="faqAccordion" style="max-width: 800px; margin: 0 auto;">
            <div class="accordion-item mb-3 bg-transparent">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1">How is the dynamic safari trip price calculated?</button>
                </h2>
                <div id="faq-1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">Fares are automatically generated based on the actual driving distance, estimated fuel consumption for the chosen vehicle category, driver daily allowances, and a vehicle maintenance reserve.</div>
                </div>
            </div>
            <div class="accordion-item mb-3 bg-transparent">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2">Does the booking include a driver and fuel?</button>
                </h2>
                <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">Yes! All standard rentals on Kong Safaris are fully chauffeured by our licensed professional guides and include base fuel allocations.</div>
                </div>
            </div>
            <div class="accordion-item mb-3 bg-transparent">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3">Can I track my booked vehicle in real time?</button>
                </h2>
                <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">Absolutely. Once a trip commences, drivers stream coordinates from their mobile workspace, allowing clients to visualize the vehicle's progress on an interactive map.</div>
                </div>
            </div>
            <div class="accordion-item mb-3 bg-transparent">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4">What payment methods do you support?</button>
                </h2>
                <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">We support secure online checkout via Paystack, accommodating M-Pesa, Card payments (Visa, Mastercard), and Airtel Money.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Banner -->
    <div class="py-5 mb-5 reveal-on-scroll bg-gradient text-center rounded-4 p-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--safari-accent) 0%, #0b5ed7 100%);">
        <div class="position-relative z-3">
            <h2 class="display-5 fw-bold text-white mb-3">Begin Your Adventure Today</h2>
            <p class="text-white opacity-75 mb-4 mx-auto" style="max-width: 600px;">Create your operator account to customize booking coordinates, view historical trip routes, and generate quotes in seconds.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?= url_to('auth.register') ?>" class="btn btn-light btn-lg px-5">Register Account</a>
                <a href="<?= url_to('auth.login') ?>" class="btn btn-outline-light btn-lg px-4">Sign In</a>
            </div>
        </div>
    </div>

    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revealElements = document.querySelectorAll('.reveal-on-scroll');
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                        const counters = entry.target.querySelectorAll('.stat-number');
                        counters.forEach(counter => triggerCounter(counter));
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            });

            revealElements.forEach(el => revealObserver.observe(el));

            function triggerCounter(counter) {
                const target = parseInt(counter.getAttribute('data-target'), 10);
                const duration = 1500;
                const stepTime = 15;
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