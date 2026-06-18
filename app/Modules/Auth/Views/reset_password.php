<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card blueprint-card p-4">
            <div class="text-center mb-4">
                <span class="fs-1"></span>
                <h2 class="mt-2 fw-bold text-accent">Reset Password</h2>
                <p class="text-muted">Enter your new password</p>
            </div>

            <form action="<?= site_url('auth/reset-password/' . $token) ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control bg-dark border-secondary text-light" id="passwordInput" name="password" placeholder="New Password" required minlength="6">
                    <label for="passwordInput" class="text-secondary">New Password (min 6 characters)</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Reset Password</button>

                <div class="text-center">
                    <p class="text-muted small mb-0"><a href="<?= url_to('auth.login') ?>" class="text-accent">Back to Sign In</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>