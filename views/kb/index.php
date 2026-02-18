<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Knowledge Base</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKBModal">
        <i class="fas fa-plus"></i> Add Document
    </button>
</div>

<div class="row g-4">
    <?php if (empty($kbs)): ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted">No documents found. Add some context for your AI tools.</p>
        </div>
    <?php else: ?>
        <?php foreach ($kbs as $kb): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="card-title text-truncate" title="<?= htmlspecialchars($kb['name']) ?>">
                                <i class="far fa-file-alt me-2 text-primary"></i> <?= htmlspecialchars($kb['name']) ?>
                            </h5>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="editKB(<?= htmlspecialchars(json_encode($kb)) ?>)">Edit</a></li>
                                    <li>
                                        <form action="/kb/<?= $kb['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this document?');">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="card-text text-muted small" style="height: 4.5em; overflow: hidden;">
                            <?= htmlspecialchars($kb['content']) ?>
                        </p>
                        <small class="text-muted">Added: <?= date('M d, Y', strtotime($kb['created_at'])) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="createKBModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/kb" method="POST" id="kbForm">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kbModalTitle">Add Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Name</label>
                        <input type="text" name="name" id="kbName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content (Text)</label>
                        <textarea name="content" id="kbContent" class="form-control" rows="10" required placeholder="Paste your context data here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Document</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function editKB(kb) {
    document.getElementById('kbModalTitle').innerText = 'Edit Document';
    document.getElementById('kbForm').action = `/kb/${kb.id}/update`;
    document.getElementById('kbName').value = kb.name;
    document.getElementById('kbContent').value = kb.content;

    var modal = new bootstrap.Modal(document.getElementById('createKBModal'));
    modal.show();
}

// Reset modal on close
document.getElementById('createKBModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('kbModalTitle').innerText = 'Add Document';
    document.getElementById('kbForm').action = '/kb';
    document.getElementById('kbName').value = '';
    document.getElementById('kbContent').value = '';
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
