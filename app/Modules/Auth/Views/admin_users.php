<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.05) !important;
        transition: background-color 0.2s ease-in-out;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-accent mb-1">User Management</h2>
        <p class="text-muted small mb-0">Manage all system users and role assignments.</p>
    </div>
    <a href="<?= url_to('auth.admin.create_user') ?>" class="btn btn-primary btn-sm">+ Create User</a>
</div>

<!-- Search & Filter -->
<div class="card blueprint-card p-3 mb-4">
    <form method="GET" action="<?= url_to('auth.admin.users') ?>" class="row g-3 align-items-end">
        <div class="col-md-5">
            <div class="form-floating">
                <input type="text" class="form-control bg-dark border-secondary text-light" name="search" value="<?= esc($search) ?>" placeholder="Search by name or email">
                <label class="text-secondary">Search Users</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select bg-dark border-secondary text-light" name="role">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="manager" <?= $roleFilter === 'manager' ? 'selected' : '' ?>>Manager</option>
                    <option value="driver" <?= $roleFilter === 'driver' ? 'selected' : '' ?>>Driver</option>
                    <option value="customer" <?= $roleFilter === 'customer' ? 'selected' : '' ?>>Customer</option>
                </select>
                <label class="text-secondary">Filter by Role</label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Apply</button>
        </div>
        <div class="col-md-2">
            <a href="<?= url_to('auth.admin.users') ?>" class="btn btn-outline-secondary w-100">Clear</a>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="card blueprint-card p-4">
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong><?= esc($u->first_name . ' ' . $u->last_name) ?></strong></td>
                            <td><?= esc($u->email) ?></td>
                            <td>
                                <span class="badge bg-<?= $u->role === 'admin' ? 'danger' : ($u->role === 'manager' ? 'warning text-dark' : ($u->role === 'driver' ? 'info' : 'secondary')) ?>">
                                    <?= esc(ucfirst($u->role)) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($u->created_at)) ?></td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?= url_to('auth.admin.edit_user', $u->id) ?>" class="btn btn-outline-info btn-sm">Edit</a>
                                    <form action="<?= url_to('auth.admin.delete_user', $u->id) ?>" method="POST" onsubmit="return confirm('Delete user? This cannot be undone.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>