<?php include __DIR__ . '/layouts/header.php'; ?>

<?php
$isEdit = isset($tool);
$action = $isEdit ? "/editor/{$tool['id']}" : "/editor";
?>

<div class="container-fluid">
    <form action="<?= $action ?>" method="POST" id="toolForm">
        <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="/dashboard" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i> Back</a>
                <h1 class="h3 mb-0 text-gray-800"><?= $isEdit ? 'Edit Tool' : 'Create New Tool' ?></h1>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i> Save Tool
            </button>
        </div>

        <div class="row">
            <!-- Left Sidebar (Navigation/Settings) -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tool Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tool['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($tool['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Uncategorized</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($tool['category_id']) && $tool['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= (isset($tool['status']) && $tool['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= (isset($tool['status']) && $tool['status'] == 'published') ? 'selected' : '' ?>>Published</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content (Tabs) -->
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="editorTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="build-tab" data-bs-toggle="tab" href="#build" role="tab">
                                    <i class="fas fa-shapes me-1"></i> Build Form
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prompt-tab" data-bs-toggle="tab" href="#prompt" role="tab">
                                    <i class="fas fa-magic me-1"></i> Prompt Logic
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="kb-tab" data-bs-toggle="tab" href="#kb" role="tab">
                                    <i class="fas fa-book-open me-1"></i> Knowledge Base
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="editorTabsContent">

                            <!-- Build Tab -->
                            <div class="tab-pane fade show active" id="build" role="tabpanel">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title">Form Fields</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField()">
                                        <i class="fas fa-plus"></i> Add Field
                                    </button>
                                </div>
                                <div id="fields-container">
                                    <!-- Fields injected via JS -->
                                </div>
                                <input type="hidden" name="fields_json" id="fields_json">
                            </div>

                            <!-- Prompt Tab -->
                            <div class="tab-pane fade" id="prompt" role="tabpanel">
                                <h5 class="card-title mb-3">AI Configuration</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Provider</label>
                                        <select name="provider" class="form-select">
                                            <option value="openai" <?= (isset($prompt['provider']) && $prompt['provider'] == 'openai') ? 'selected' : '' ?>>OpenAI</option>
                                            <option value="deepseek" <?= (isset($prompt['provider']) && $prompt['provider'] == 'deepseek') ? 'selected' : '' ?>>DeepSeek</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Model</label>
                                        <input type="text" name="model" class="form-control" value="<?= htmlspecialchars($prompt['model'] ?? 'gpt-3.5-turbo') ?>">
                                        <small class="text-muted">e.g. gpt-4, deepseek-chat</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">System Message</label>
                                    <textarea name="system_message" class="form-control" rows="5" placeholder="You are a helpful assistant..."><?= htmlspecialchars($prompt['system_message'] ?? '') ?></textarea>
                                    <small class="text-muted">Global instructions for the AI.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">User Message Template</label>
                                    <textarea name="user_message_template" class="form-control" rows="5" placeholder="Write a blog post about {{topic}}..."><?= htmlspecialchars($prompt['user_message_template'] ?? '') ?></textarea>
                                    <small class="text-muted">Use <code>{{variable_name}}</code> to insert field values.</small>
                                </div>
                            </div>

                            <!-- Knowledge Base Tab -->
                            <div class="tab-pane fade" id="kb" role="tabpanel">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title">Attach Context</h5>
                                    <a href="/kb" class="btn btn-sm btn-outline-primary">Manage Knowledge Base</a>
                                </div>

                                <?php if (empty($knowledge_bases)): ?>
                                    <div class="alert alert-info">No knowledge base documents found. Create one first!</div>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($knowledge_bases as $kb): ?>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-1" type="checkbox" name="kb_ids[]" value="<?= $kb['id'] ?>"
                                                    <?= (isset($linked_kb_ids) && in_array($kb['id'], $linked_kb_ids)) ? 'checked' : '' ?>>
                                                <?= htmlspecialchars($kb['name']) ?>
                                                <small class="text-muted d-block"><?= substr(htmlspecialchars($kb['content']), 0, 50) ?>...</small>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Field Template (Hidden) -->
<template id="field-template">
    <div class="card mb-3 field-item border-start border-4 border-primary">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-secondary field-index">Field #1</span>
                <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeField(this)"><i class="fas fa-trash"></i></button>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small">Label</label>
                    <input type="text" class="form-control form-control-sm field-label" onchange="updateJson()">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Variable Name</label>
                    <input type="text" class="form-control form-control-sm field-variable" placeholder="e.g. topic" onchange="updateJson()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Type</label>
                    <select class="form-select form-select-sm field-type" onchange="updateJson()">
                        <option value="text">Text Input</option>
                        <option value="textarea">Text Area</option>
                        <option value="select">Dropdown</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Placeholder</label>
                    <input type="text" class="form-control form-control-sm field-placeholder" onchange="updateJson()">
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input field-required" type="checkbox" checked onchange="updateJson()">
                        <label class="form-check-label small">Required</label>
                    </div>
                </div>
                <div class="col-12 mt-2" style="display:none;" class="field-options-container">
                    <label class="form-label small">Options (Comma separated)</label>
                    <input type="text" class="form-control form-control-sm field-options" placeholder="Option 1, Option 2" onchange="updateJson()">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let fields = <?= isset($fields) ? json_encode($fields) : '[]' ?>;

function renderFields() {
    const container = document.getElementById('fields-container');
    container.innerHTML = '';

    fields.forEach((field, index) => {
        const template = document.getElementById('field-template').content.cloneNode(true);
        const card = template.querySelector('.field-item');

        card.querySelector('.field-index').textContent = `Field #${index + 1}`;
        card.querySelector('.field-label').value = field.label || '';
        card.querySelector('.field-variable').value = field.variable_name || '';
        card.querySelector('.field-type').value = field.type || 'text';
        card.querySelector('.field-placeholder').value = field.placeholder || '';
        card.querySelector('.field-required').checked = field.required !== false; // default true

        // Handle Options
        const optionsContainer = card.querySelector('.col-12'); // Options container
        if (field.type === 'select') {
             // If field.options is array (from JSON decode in PHP), join it.
             // If it's string (from simple input), keep it.
             let opts = field.options;
             if (Array.isArray(opts)) opts = opts.join(', ');
             card.querySelector('.field-options').value = opts || '';
             card.querySelector('.col-12').style.display = 'block';
        } else {
             card.querySelector('.col-12').style.display = 'none';
        }

        // Add event listener for type change to toggle options
        card.querySelector('.field-type').addEventListener('change', function() {
            const isSelect = this.value === 'select';
            this.closest('.row').querySelector('.col-12').style.display = isSelect ? 'block' : 'none';
        });

        container.appendChild(card);
    });
    updateJson();
}

function addField() {
    fields.push({
        label: 'New Field',
        variable_name: 'field_' + (fields.length + 1),
        type: 'text',
        required: true,
        options: []
    });
    renderFields();
}

function removeField(btn) {
    const card = btn.closest('.field-item');
    const index = Array.from(card.parentNode.children).indexOf(card);
    fields.splice(index, 1);
    renderFields();
}

function updateJson() {
    const container = document.getElementById('fields-container');
    const newFields = [];

    container.querySelectorAll('.field-item').forEach((item) => {
        const type = item.querySelector('.field-type').value;
        const optsRaw = item.querySelector('.field-options').value;

        newFields.push({
            label: item.querySelector('.field-label').value,
            variable_name: item.querySelector('.field-variable').value,
            type: type,
            placeholder: item.querySelector('.field-placeholder').value,
            required: item.querySelector('.field-required').checked,
            options: type === 'select' ? optsRaw.split(',').map(s => s.trim()).filter(s => s) : []
        });
    });

    fields = newFields;
    document.getElementById('fields_json').value = JSON.stringify(fields);
}

// Initial render
document.addEventListener('DOMContentLoaded', renderFields);

// Handle form submission to ensure JSON is updated
document.getElementById('toolForm').addEventListener('submit', updateJson);

</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
