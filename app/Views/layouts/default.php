<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?= esc($pageTitle ?? 'Kong Safaris Fleet & Booking System') ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>">
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
    <meta property="og:image" content="<?= esc($metaImage ?? base_url('assets/img/safari-cover.jpg')) ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= esc($canonicalUrl ?? current_url()) ?>">
    <meta name="twitter:title" content="<?= esc($pageTitle ?? 'Kong Safaris Fleet & Booking System') ?>">
    <meta name="twitter:description" content="<?= esc($metaDescription ?? 'Manage pricing, booking, and real-time fleet tracking for Kong Safaris.') ?>">
    <meta name="twitter:image" content="<?= esc($metaImage ?? base_url('assets/img/safari-cover.jpg')) ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (Local example asset) -->
    <link rel="stylesheet" href="<?= base_url('bootstrap-5.3.8-examples/assets/dist/css/bootstrap.min.css') ?>">
    
    <!-- Custom theme variables & micro-animations -->
    <style>
        :root {
            --safari-primary: #1e3f20; /* Deep Forest Green */
            --safari-accent: #d4af37;  /* Safari Gold */
            --safari-accent-rgb: 212, 175, 55;
            --safari-bg-dark: #121813; /* Very Dark Charcoal/Green */
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

        .nav-link:hover, .nav-link.active {
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
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            color: var(--safari-accent);
            opacity: 0.85;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--safari-accent);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
    </style>
    
    <!-- Yield additional styles -->
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url() ?>">
                <span class="fs-4">🦁 KONG SAFARIS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/dashboard') ?>">Dashboard</a>
                        </li>
                        <?php if (session()->get('role') === 'customer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('trips/quote') ?>">Book a Safari</a>
                            </li>
                        <?php endif; ?>
                        <?php if (session()->get('role') === 'manager' || session()->get('role') === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('trips/manager') ?>">Manage Bookings</a>
                            </li>
                        <?php endif; ?>
                        <?php if (session()->get('role') === 'driver'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('trips/driver') ?>">Driver Workspace</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <span class="text-light me-3 small">
                            Logged in as: <strong class="text-accent"><?= esc(session()->get('first_name')) ?> (<?= esc(ucfirst(session()->get('role'))) ?>)</strong>
                        </span>
                        <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-primary btn-sm px-4">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content wrapper -->
    <main class="py-4">
        <div class="container">
            <!-- Global Flash Messages -->
            <?= $this->include('partials/flash_messages') ?>
            
            <!-- Render page-specific content -->
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-1">&copy; <?= date('Y') ?> Kong Safaris Ltd. All rights reserved.</p>
            <small class="text-muted">Dynamic Fleet Pricing, Tracking & Booking Operations</small>
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
        });
    </script>
    
    <!-- Yield additional scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
