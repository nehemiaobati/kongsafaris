<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?= esc($pageTitle ?? 'Kong Safaris Fleet & Booking System') ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>">
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'safari, transport, booking, fleet, kenya') ?>">
    <link rel="canonical" href="<?= esc($canonicalUrl ?? current_url()) ?>">
    <meta name="robots" content="<?= esc($robotsTag ?? 'noindex, nofollow') ?>">

    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="X-CSRF-TOKEN">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= esc($canonicalUrl ?? current_url()) ?>">
    <meta property="og:title" content="<?= esc($pageTitle ?? 'Kong Safaris Fleet & Booking System') ?>">
    <meta property="og:description" content="<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>">
    <meta property="og:image" content="<?= esc($metaImage ?? base_url('assets/img/safari-hero.png')) ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= esc($canonicalUrl ?? current_url()) ?>">
    <meta name="twitter:title" content="<?= esc($pageTitle ?? 'Kong Safaris Fleet & Booking System') ?>">
    <meta name="twitter:description" content="<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>">
    <meta name="twitter:image" content="<?= esc($metaImage ?? base_url('assets/img/safari-hero.png')) ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (Local example asset) -->
    <link rel="stylesheet" href="<?= base_url('bootstrap-5.3.8-examples/assets/dist/css/bootstrap.min.css') ?>">

    <!-- JSON-LD Structured Data for Google Rich Results -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "TravelAgency",
      "name": "Kong Safaris",
      "image": "<?= base_url('assets/img/safari-hero.png') ?>",
      "description": "<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Nairobi",
        "addressCountry": "KE"
      },
      "priceRange": "$$"
    }
    </script>

    <!-- Custom theme variables & micro-animations -->
    <style>
        :root {
            --safari-primary: #1e3f20;
            /* Deep Forest Green */
            --safari-accent: #d4af37;
            /* Safari Gold */
            --safari-accent-rgb: 212, 175, 55;
            --safari-bg-dark: #121813;
            /* Very Dark Charcoal/Green */
            --safari-card-bg: rgba(30, 47, 32, 0.25);
            --font-family-sans-serif: 'Outfit', sans-serif;
        }

        body {
            font-family: var(--font-family-sans-serif);
            background-color: var(--safari-bg-dark);
            color: #f1f3f2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: rgba(18, 24, 19, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--safari-accent) !important;
            letter-spacing: 1px;
        }

        .nav-link {
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--safari-accent) !important;
        }

        .blueprint-card {
            background: var(--safari-card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .blueprint-card:hover {
            border-color: rgba(212, 175, 55, 0.3);
        }

        .btn-primary {
            background-color: var(--safari-accent);
            border-color: var(--safari-accent);
            color: #121813;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #c29e2e;
            border-color: #c29e2e;
            color: #121813;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .btn-outline-secondary {
            color: #f1f3f2;
            border-color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
        }

        .btn-outline-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: #ffffff;
        }

        .text-accent {
            color: var(--safari-accent) !important;
        }

        footer {
            margin-top: auto;
            background-color: #0d120e;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px 0;
            font-size: 0.9rem;
            color: #8c9c90;
        }

        /* Float labels styling compatibility */
        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label,
        .form-floating>.form-select~label {
            color: var(--safari-accent);
            opacity: 0.85;
        }

        .hover-link {
            transition: color 0.2s ease-in-out;
        }
        .hover-link:hover {
            color: var(--safari-accent) !important;
        }
        :focus-visible {
            outline: 2px solid var(--safari-accent) !important;
            outline-offset: 3px !important;
            border-radius: 4px;
        }
    </style>

    <!-- Yield additional styles -->
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <a href="#main-content" class="visually-hidden-focusable position-absolute top-0 start-0 bg-primary text-white p-2" style="z-index: 9999;">Skip to main content</a>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= url_to('auth.dashboard') ?>">
                <span class="fs-4">🦁 KONG SAFARIS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <!-- Dashboard (all roles) -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url_to('auth.dashboard') ?>">Dashboard</a>
                        </li>

                        <!-- Customer navigation -->
                        <?php if (session()->get('role') === 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.quote') ?>">Book a Safari</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.customer.dashboard') ?>">My Bookings</a>
                            </li>
                        <?php endif; ?>

                        <!-- Manager / Admin navigation -->
                        <?php if (in_array(session()->get('role'), ['manager', 'admin'], true)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.manager') ?>">Manage Bookings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.reports') ?>">Reports</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.manager.manual_booking') ?>">Manual Booking</a>
                            </li>
                        <?php endif; ?>

                        <!-- Driver navigation -->
                        <?php if (session()->get('role') === 'driver'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url_to('trips.driver') ?>">Driver Workspace</a>
                            </li>
                        <?php endif; ?>

                        <!-- Profile (all roles) -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url_to('auth.profile') ?>">Profile</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('#fleet') ?>">Our Fleet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('#destinations') ?>">Destinations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('#how-it-works') ?>">How It Works</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('#faq') ?>">FAQ</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Right side: user info & auth actions -->
                <div class="d-flex align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <span class="text-light me-3 small">
                            Logged in as: <strong class="text-accent"><?= esc(session()->get('first_name')) ?> (<?= esc(ucfirst(session()->get('role'))) ?>)</strong>
                        </span>
                        <a href="<?= url_to('auth.logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
                    <?php else: ?>
                        <a href="<?= url_to('auth.login') ?>" class="btn btn-outline-secondary btn-sm me-2">Sign In</a>
                        <a href="<?= url_to('auth.register') ?>" class="btn btn-primary btn-sm px-4">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content wrapper -->
    <main id="main-content" class="py-4">
        <div class="container">
            <!-- Global Flash Messages -->
            <?= $this->include('partials/flash_messages') ?>

            <!-- Render page-specific content -->
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row g-4 text-start mb-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-accent fw-bold mb-3">🦁 KONG SAFARIS</h5>
                    <p class="small text-muted">
                        Providing premium safari vehicle rentals, customized tour transport, and real-time fleet operations management across Kenya. Reimagining wild adventures with safety and ease.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= base_url() ?>" class="text-muted text-decoration-none hover-link">Home</a></li>
                        <li class="mb-2"><a href="<?= base_url('#fleet') ?>" class="text-muted text-decoration-none hover-link">Our Fleet</a></li>
                        <li class="mb-2"><a href="<?= base_url('#destinations') ?>" class="text-muted text-decoration-none hover-link">Destinations</a></li>
                        <li class="mb-2"><a href="<?= base_url('#how-it-works') ?>" class="text-muted text-decoration-none hover-link">How It Works</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Top Destinations</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= base_url('#destinations') ?>" class="text-muted text-decoration-none hover-link">Maasai Mara National Reserve</a></li>
                        <li class="mb-2"><a href="<?= base_url('#destinations') ?>" class="text-muted text-decoration-none hover-link">Amboseli National Park</a></li>
                        <li class="mb-2"><a href="<?= base_url('#destinations') ?>" class="text-muted text-decoration-none hover-link">Lake Nakuru</a></li>
                        <li class="mb-2"><a href="<?= base_url('#destinations') ?>" class="text-muted text-decoration-none hover-link">Tsavo East & West</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Contact Us</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">📍 Nairobi, Kenya</li>
                        <li class="mb-2">📞 +254 700 000000</li>
                        <li class="mb-2">✉️ info@kongsafaris.com</li>
                        <li class="mb-2">🕒 Mon - Sat: 8:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary opacity-25 mb-4">
            <div class="text-center">
                <p class="mb-1 text-muted">&copy; <?= date('Y') ?> Kong Safaris Ltd. All rights reserved.</p>
                <small class="text-muted">Dynamic Fleet Pricing, Tracking & Booking Operations</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS (Local example asset) -->
    <script src="<?= base_url('bootstrap-5.3.8-examples/assets/dist/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- jQuery or helper function for forms CSRF updates in dynamic content -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper to automatically inject CSRF token into all AJAX requests via headers
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfHeader = document.querySelector('meta[name="csrf-header"]').getAttribute('content');

            window.getCSRFToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            window.updateCSRFToken = (newHash) => {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', newHash);
                // Also update all input fields with name csrf_test_name
                document.querySelectorAll('input[name="csrf_test_name"]').forEach(input => {
                    input.value = newHash;
                });
            };

            // Global smart smooth scroll handler for base_url('#hash') links
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.href) return;
                    try {
                        const url = new URL(this.href, window.location.href);
                        if (url.pathname === window.location.pathname && url.hash) {
                            const targetElement = document.querySelector(url.hash);
                            if (targetElement) {
                                e.preventDefault();
                                targetElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                                history.pushState(null, '', url.hash);
                            }
                        }
                    } catch (err) {
                        // Ignore invalid URLs
                    }
                });
            });
        });
    </script>

    <!-- Yield additional scripts -->
    <?= $this->renderSection('scripts') ?>
</body>

</html>