<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($tool) ? 'Edit Tool' : 'Create New Tool'; ?> - FormWise Clone</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Editor Specific Overrides */

        .editor-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        .back-link:hover {
            color: var(--primary-blue);
        }
        .nav-menu {
            padding: 1rem 0;
            flex-grow: 1;
        }
        .nav-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-left: 3px solid transparent;
            cursor: pointer;
            font-weight: 500;
        }
        .nav-item:hover {
            background-color: #F9FAFB;
            color: var(--primary-blue);
        }
        .nav-item.active {
            background-color: #EEF2FF;
            color: var(--primary-blue);
            border-left-color: var(--primary-blue);
        }

        /* Main Content */
        .editor-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 0;
            display: flex;
            flex-direction: column;
            background-color: #F3F4F6; /* Light gray bg */
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        .tool-title-input {
            border: none;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            width: 300px;
            background: transparent;
        }
        .tool-title-input:focus {
            outline: none;
            background: #F9FAFB;
        }

        /* Content Area */
        .content-area {
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-primary);
        }
        .card-body {
            padding: 1.5rem;
        }

        /* Form Fields */
        .field-item {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .field-item:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: #D1D5DB;
        }
        .field-handle {
            cursor: grab;
            color: #9CA3AF; /* Gray 400 */
            margin-right: 0.75rem;
        }
        .remove-field-btn {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            color: #EF4444; /* Red 500 */
            background: none;
            border: none;
            font-size: 0.875rem;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .remove-field-btn:hover {
            opacity: 1;
        }

        .code-snippet {
            background: #1F2937;
            color: #E5E7EB;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.85rem;
        }

        .btn-preview {
            color: var(--text-secondary);
            border-color: var(--border-color);
            background: white;
            font-weight: 500;
        }
        .btn-preview:hover {
            background: #F9FAFB;
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <div class="editor-sidebar">
        <div class="sidebar-header">
            <a href="/tools" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="nav-menu">
            <div class="nav-item">
                <i class="fa-solid fa-gear width-20"></i> App Settings
            </div>
            <div class="nav-item active">
                <i class="fa-solid fa-screwdriver-wrench width-20"></i> Build Tool
            </div>
            <div class="nav-item">
                <i class="fa-solid fa-palette width-20"></i> Theme Settings
            </div>
            <div class="nav-item">
                <i class="fa-solid fa-share-nodes width-20"></i> Share & Embed
            </div>
            <div class="nav-item">
                <i class="fa-solid fa-plug width-20"></i> Integrations
            </div>
            <div class="nav-item">
                <i class="fa-solid fa-chart-pie width-20"></i> Results
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="editor-content">
        <form id="toolForm" method="POST" action="<?php echo isset($tool) ? '/tools/update/' . $tool['id'] : '/tools'; ?>">
            <?php echo CSRF::field(); ?>

            <!-- Top Bar -->
            <div class="top-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light rounded p-2 text-primary" style="background-color: #EEF2FF !important;">
                        <i class="fa-solid fa-robot fa-lg" style="color: var(--primary-blue);"></i>
                    </div>
                    <div>
                        <input type="text" name="title" class="tool-title-input"
                               value="<?php echo isset($tool) ? htmlspecialchars($tool['title']) : 'Untitled Tool'; ?>"
                               placeholder="Enter tool name" required>
                        <div class="small text-secondary fw-normal">
                            slug: <span id="slugDisplay" class="font-monospace"><?php echo isset($tool) ? htmlspecialchars($tool['slug']) : 'untitled-tool'; ?></span>
                        </div>
                        <input type="hidden" name="slug" id="slugInput" value="<?php echo isset($tool) ? htmlspecialchars($tool['slug']) : ''; ?>">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?php echo isset($tool) ? '/tool/' . $tool['slug'] : '#'; ?>" target="_blank" class="btn btn-preview btn-sm shadow-sm <?php echo isset($tool) ? '' : 'disabled'; ?>">
                        <i class="fa-solid fa-external-link-alt me-1"></i> Preview
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm fw-medium px-3">
                        <i class="fa-solid fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>

            <!-- Scrollable Area -->
            <div class="content-area">

                <!-- AI Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <span><i class="fa-solid fa-brain me-2 text-primary"></i> AI Engine Configuration</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">AI Provider</label>
                                <select name="model_provider" class="form-select bg-light">
                                    <option value="openai" <?php echo (isset($tool) && $tool['model_provider'] == 'openai') ? 'selected' : ''; ?>>OpenAI</option>
                                    <option value="deepseek" <?php echo (isset($tool) && $tool['model_provider'] == 'deepseek') ? 'selected' : ''; ?>>DeepSeek</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Model Name</label>
                                <input type="text" name="model_name" class="form-control bg-light"
                                       value="<?php echo isset($tool) ? htmlspecialchars($tool['model_name']) : 'gpt-3.5-turbo'; ?>"
                                       placeholder="e.g. gpt-4">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">System Prompt</label>
                                <div class="form-text mb-2 text-secondary">
                                    Define the AI's persona and instructions. Use <code class="bg-light px-1 rounded border">{variable_name}</code> to insert dynamic user inputs.
                                </div>
                                <textarea name="system_prompt" class="form-control font-monospace bg-light" rows="6" required
                                          placeholder="You are a helpful assistant..."><?php echo isset($tool) ? htmlspecialchars($tool['system_prompt']) : ''; ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Context Data (Knowledge Base)</label>
                                <div class="form-text mb-2 text-secondary">
                                    Static information the AI should know (e.g., FAQs, Brand Guidelines).
                                </div>
                                <textarea name="context_data" class="form-control font-monospace bg-light" rows="4"
                                          placeholder="Paste your context data here..."><?php echo isset($tool) ? htmlspecialchars($tool['context_data']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Builder -->
                <div class="card">
                    <div class="card-header">
                        <span><i class="fa-solid fa-list-check me-2 text-primary"></i> Form Fields</span>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-medium" onclick="addField()">
                            <i class="fa-solid fa-plus me-1"></i> Add Field
                        </button>
                    </div>
                    <div class="card-body bg-light">
                        <div id="fields-container">
                            <!-- Fields injected via JS -->
                        </div>

                        <div id="empty-state" class="text-center py-5 text-secondary" style="display:none;">
                            <div class="mb-3">
                                <span class="fa-stack fa-2x">
                                  <i class="fa-solid fa-circle fa-stack-2x text-white"></i>
                                  <i class="fa-solid fa-clipboard-list fa-stack-1x text-muted"></i>
                                </span>
                            </div>
                            <p class="mb-0 fw-medium">No fields yet.</p>
                            <p class="small text-muted">Click "Add Field" to start building your input form.</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="fields_json" id="fieldsJson">

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // State
    let fields = <?php echo isset($tool['fields']) ? json_encode($tool['fields']) : '[]'; ?>;

    // Auto-slug generation
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.getElementById('slugInput');
    const slugDisplay = document.getElementById('slugDisplay');

    titleInput.addEventListener('input', function() {
        <?php if (!isset($tool)): ?>
        const slug = this.value.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        slugInput.value = slug;
        slugDisplay.textContent = slug || 'untitled-tool';
        <?php endif; ?>
    });

    // Field Builder Logic
    function escapeHtml(unsafe) {
        if (!unsafe) return "";
        return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
    }

    function renderFields() {
        const container = document.getElementById('fields-container');
        const emptyState = document.getElementById('empty-state');

        container.innerHTML = '';

        if (fields.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }

        fields.forEach((field, index) => {
            const div = document.createElement('div');
            div.className = 'field-item';

            const safeLabel = escapeHtml(field.label);
            const safeName = escapeHtml(field.field_name);
            const safeOptions = escapeHtml(Array.isArray(field.options) ? field.options.join(', ') : (field.options || ''));

            div.innerHTML = `
                <button type="button" class="remove-field-btn" onclick="removeField(${index})" title="Remove Field">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
                <div class="row g-3 align-items-center">
                    <div class="col-auto d-flex align-items-center">
                        <span class="field-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                        <span class="badge bg-light text-secondary border rounded-pill">#${index + 1}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Field Label</label>
                        <input type="text" class="form-control form-control-sm" value="${safeLabel}"
                               oninput="updateField(${index}, 'label', this.value)" placeholder="e.g. Topic">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Variable (Prompt)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted">{</span>
                            <input type="text" class="form-control font-monospace" value="${safeName}"
                                   oninput="updateField(${index}, 'field_name', this.value)" placeholder="topic">
                            <span class="input-group-text bg-light text-muted">}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Input Type</label>
                        <select class="form-select form-select-sm" onchange="updateField(${index}, 'field_type', this.value)">
                            <option value="text" ${field.field_type === 'text' ? 'selected' : ''}>Short Text</option>
                            <option value="textarea" ${field.field_type === 'textarea' ? 'selected' : ''}>Long Text</option>
                            <option value="select" ${field.field_type === 'select' ? 'selected' : ''}>Dropdown Select</option>
                        </select>
                    </div>
                </div>

                ${field.field_type === 'select' ? `
                <div class="mt-3 ps-5 border-top pt-2">
                    <label class="form-label small text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Dropdown Options (Comma Separated)</label>
                    <input type="text" class="form-control form-control-sm" value="${safeOptions}"
                           oninput="updateOptions(${index}, this.value)" placeholder="Option 1, Option 2, Option 3">
                </div>
                ` : ''}
            `;
            container.appendChild(div);
        });

        document.getElementById('fieldsJson').value = JSON.stringify(fields);
    }

    function addField() {
        fields.push({
            label: '',
            field_name: '',
            field_type: 'text',
            options: []
        });
        renderFields();
    }

    function removeField(index) {
        if (confirm('Delete this field?')) {
            fields.splice(index, 1);
            renderFields();
        }
    }

    function updateField(index, key, value) {
        fields[index][key] = value;
        if (key === 'field_type') {
            renderFields();
        } else {
            // Just update JSON if not re-rendering
            document.getElementById('fieldsJson').value = JSON.stringify(fields);
        }
    }

    function updateOptions(index, value) {
        fields[index].options = value.split(',').map(s => s.trim());
        document.getElementById('fieldsJson').value = JSON.stringify(fields);
    }

    // Initial render
    renderFields();

    // Form submit handler
    document.getElementById('toolForm').addEventListener('submit', function() {
        document.getElementById('fieldsJson').value = JSON.stringify(fields);
    });

</script>
</body>
</html>
