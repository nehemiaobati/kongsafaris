<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-6">
        <div class="card blueprint-card p-4">
            <div class="text-center mb-4">
                <h2 class="mt-2 fw-bold text-accent">Create Account</h2>
                <p class="text-muted">Join Kong Safaris to book & track your safari trips</p>
            </div>

            <form action="<?= url_to('auth.register.submit') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="firstNameInput" name="first_name" placeholder="First Name" value="<?= old('first_name') ?>" required>
                            <label for="firstNameInput">First Name</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="lastNameInput" name="last_name" placeholder="Last Name" value="<?= old('last_name') ?>" required>
                            <label for="lastNameInput">Last Name</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mt-3">
                    <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                    <label for="emailInput">Email address</label>
                </div>

                <div class="form-floating mt-3">
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password" required minlength="6">
                    <label for="passwordInput">Password (min 6 characters)</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary mt-4" type="submit">Create Account</button>

                <div class="text-center mt-3">
                    <p class="text-muted small mb-0">Already have an account? <a href="<?= url_to('auth.login') ?>" class="text-accent">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>