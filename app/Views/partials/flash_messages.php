<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show blueprint-card border-success border-opacity-25" role="alert">
        <div class="d-flex align-items-center">
            <span class="me-2">✅</span>
            <div><?= esc(session()->getFlashdata('success')) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show blueprint-card border-danger border-opacity-25" role="alert">
        <div class="d-flex align-items-center">
            <span class="me-2">⚠️</span>
            <div><?= esc(session()->getFlashdata('error')) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show blueprint-card border-danger border-opacity-25" role="alert">
        <div class="d-flex align-items-center mb-1">
            <span class="me-2">⚠️</span>
            <div><strong>Please correct the following errors:</strong></div>
        </div>
        <ul class="mb-0 ps-4">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
