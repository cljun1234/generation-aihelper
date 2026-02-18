<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Tools</h1>
    <a href="/editor" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Tool
    </a>
</div>

<?php if (empty($tools)): ?>
    <div class="text-center py-5">
        <div class="mb-3 text-muted">
            <i class="fas fa-robot fa-4x"></i>
        </div>
        <h4>No tools yet</h4>
        <p class="text-muted">Get started by creating your first AI tool.</p>
        <a href="/editor" class="btn btn-primary mt-2">Create Tool</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($tools as $tool): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 tool-card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="tool-icon bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas <?= htmlspecialchars($tool['icon'] ?: 'fa-robot') ?>"></i>
                            </div>
                            <span class="badge bg-<?= $tool['status'] === 'published' ? 'success' : 'secondary' ?> rounded-pill">
                                <?= ucfirst($tool['status']) ?>
                            </span>
                        </div>

                        <h5 class="card-title fw-bold text-dark mb-1">
                            <?= htmlspecialchars($tool['name']) ?>
                        </h5>

                        <?php if ($tool['category_name']): ?>
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-folder me-1"></i> <?= htmlspecialchars($tool['category_name']) ?>
                            </small>
                        <?php endif; ?>

                        <p class="card-text text-muted small" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 4.5em;">
                            <?= htmlspecialchars($tool['description'] ?: 'No description provided.') ?>
                        </p>
                    </div>

                    <div class="card-footer bg-transparent border-top-0 pb-3 pt-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="/editor/<?= $tool['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="/tool/<?= $tool['slug'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Preview Public Link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>

                            <form action="/tool/delete/<?= $tool['id'] ?>" method="POST" onsubmit="return confirm('Are you sure?');" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                                <button type="submit" class="btn btn-link text-danger p-0 ms-2" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/layouts/footer.php'; ?>
