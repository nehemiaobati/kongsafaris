<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card blueprint-card p-4">
            <h4 class="fw-bold text-accent mb-4"><?= $mode === 'create' ? 'Create New User' : 'Edit User' ?></h4>

            <form action="<?= $mode === 'create' ? url_to('auth.admin.create_user.submit') : url_to('auth.admin.edit_user.submit', (string)$user->id) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="first_name" value="<?= esc($mode === 'edit' ? $user->first_name : '') ?>" required>
                    <label>First Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="last_name" value="<?= esc($mode === 'edit' ? $user->last_name : '') ?>" required>
                    <label>Last Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" value="<?= esc($mode === 'edit' ? $user->email : '') ?>" required>
                    <label>Email Address</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" <?= $mode === 'create' ? 'required' : '' ?>>
                    <label><?= $mode === 'create' ? 'Password' : 'New Password (leave blank to keep current)' ?></label>
                </div>

                <div class="form-floating mb-4">
                    <select class="form-select" name="role" required>
                        <option value="admin" <?= $mode === 'edit' && $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="manager" <?= $mode === 'edit' && $user->role === 'manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="driver" <?= $mode === 'edit' && $user->role === 'driver' ? 'selected' : '' ?>>Driver</option>
                        <option value="customer" <?= $mode === 'edit' && $user->role === 'customer' ? 'selected' : '' ?>>Customer</option>
                    </select>
                    <label>Role</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><?= $mode === 'create' ? 'Create User' : 'Update User' ?></button>
                    <a href="<?= url_to('auth.admin.users') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>