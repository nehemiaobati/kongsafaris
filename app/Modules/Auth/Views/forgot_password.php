<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card blueprint-card p-4">
            <div class="text-center mb-4">
                <span class="fs-1">🔑</span>
                <h2 class="mt-2 fw-bold text-accent">Forgot Password</h2>
                <p class="text-muted">Enter your email to receive a reset link</p>
            </div>

            <form action="<?= url_to('auth.forgot.submit') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-floating mb-4">
                    <input type="email" class="form-control bg-dark border-secondary text-light" id="emailInput" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                    <label for="emailInput" class="text-secondary">Email address</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Send Reset Link</button>

                <div class="text-center">
                    <p class="text-muted small mb-0"><a href="<?= url_to('auth.login') ?>" class="text-accent">Back to Sign In</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>