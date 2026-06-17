<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card blueprint-card p-4">
            <div class="text-center mb-4">
                <span class="fs-1">🦁</span>
                <h2 class="mt-2 fw-bold text-accent">Kong Safaris</h2>
                <p class="text-muted">Fleet Operations & Booking System</p>
            </div>

            <form action="<?= url_to('auth.login.submit') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control bg-dark border-secondary text-light" id="emailInput" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                    <label for="emailInput" class="text-secondary">Email address</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control bg-dark border-secondary text-light" id="passwordInput" name="password" placeholder="Password" required>
                    <label for="passwordInput" class="text-secondary">Password</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Sign In</button>

                <div class="text-center text-muted small mt-2">
                    <p class="mb-0">Demo Credentials:</p>
                    <code class="text-accent">manager@kongsafaris.com</code> / <code class="text-accent">manager123</code><br>
                    <code class="text-accent">driver@kongsafaris.com</code> / <code class="text-accent">driver123</code><br>
                    <code class="text-accent">customer@kongsafaris.com</code> / <code class="text-accent">customer123</code>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
