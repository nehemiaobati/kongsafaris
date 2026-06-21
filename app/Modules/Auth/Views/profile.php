<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card blueprint-card p-4">
            <div class="text-center mb-4">
                <h2 class="mt-2 fw-bold text-accent">My Profile</h2>
                <p class="text-muted">Update your account information</p>
            </div>

            <form action="<?= url_to('auth.profile.update') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="firstNameInput" name="first_name" placeholder="First Name" value="<?= esc($user->first_name) ?>" required>
                            <label for="firstNameInput">First Name</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="lastNameInput" name="last_name" placeholder="Last Name" value="<?= esc($user->last_name) ?>" required>
                            <label for="lastNameInput">Last Name</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mt-3">
                    <input type="email" class="form-control" id="emailInput" value="<?= esc($user->email) ?>" disabled>
                    <label for="emailInput">Email address (cannot be changed)</label>
                </div>

                <hr class="my-4">

                <p class="text-muted small mb-3">Leave password blank to keep current password.</p>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="New Password" minlength="6">
                    <label for="passwordInput">New Password</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Update Profile</button>

                <div class="text-center mt-3">
                    <a href="<?= url_to('auth.dashboard') ?>" class="text-accent small">&larr; Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>