<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($tool) ? 'Edit Tool' : 'Create Tool'; ?> - AI Tools</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        header { background: white; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: bold; font-size: 1.2rem; color: #1a73e8; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #555; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 5px; font-weight: 500; color: #333; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        textarea { min-height: 100px; resize: vertical; }
        .btn { padding: 10px 20px; background: #1a73e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn-secondary { background: #6c757d; }
        .btn-danger { background: #dc3545; }
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }

        /* Field Builder Styles */
        .field-item { background: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; margin-bottom: 10px; border-radius: 4px; position: relative; }
        .field-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: bold; color: #555; }
        .remove-field { color: #dc3545; cursor: pointer; font-size: 0.9rem; text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <div class="logo">AI Tools Builder</div>
        <div class="nav-links">
            <a href="/tools">Back to Tools</a>
        </div>
    </header>

    <div class="container">
        <form id="toolForm" method="POST" action="<?php echo isset($tool) ? '/tools/update/' . $tool['id'] : '/tools'; ?>">

            <!-- General Settings -->
            <div class="card">
                <h2>General Settings</h2>
                <div class="row">
                    <div class="col form-group">
                        <label>Tool Name</label>
                        <input type="text" name="title" value="<?php echo isset($tool) ? htmlspecialchars($tool['title']) : ''; ?>" required oninput="generateSlug(this.value)">
                    </div>
                    <div class="col form-group">
                        <label>URL Slug (Unique)</label>
                        <input type="text" name="slug" id="slugInput" value="<?php echo isset($tool) ? htmlspecialchars($tool['slug']) : ''; ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>AI Provider</label>
                        <select name="model_provider">
                            <option value="openai" <?php echo (isset($tool) && $tool['model_provider'] == 'openai') ? 'selected' : ''; ?>>OpenAI</option>
                            <option value="deepseek" <?php echo (isset($tool) && $tool['model_provider'] == 'deepseek') ? 'selected' : ''; ?>>DeepSeek</option>
                        </select>
                    </div>
                    <div class="col form-group">
                        <label>Model Name</label>
                        <input type="text" name="model_name" value="<?php echo isset($tool) ? htmlspecialchars($tool['model_name']) : 'gpt-3.5-turbo'; ?>" placeholder="e.g. gpt-4 or deepseek-chat">
                    </div>
                </div>
            </div>

            <!-- Prompt Engineering -->
            <div class="card">
                <h2>Prompt Engineering</h2>
                <div class="form-group">
                    <label>System Prompt</label>
                    <p style="font-size: 0.85rem; color: #666; margin-top: -5px;">
                        Instructions for the AI. Use <code>{variable_name}</code> to insert form field values dynamically.
                    </p>
                    <textarea name="system_prompt" required placeholder="You are a helpful assistant that writes blog posts about {topic}."><?php echo isset($tool) ? htmlspecialchars($tool['system_prompt']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Context Data (Knowledge Base)</label>
                    <p style="font-size: 0.85rem; color: #666; margin-top: -5px;">
                        Static text to append to the prompt (e.g., brand guidelines, faqs).
                    </p>
                    <textarea name="context_data" placeholder="Paste your knowledge base content here..."><?php echo isset($tool) ? htmlspecialchars($tool['context_data']) : ''; ?></textarea>
                </div>
            </div>

            <!-- Form Builder -->
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px;">
                    <h2 style="border:none; margin:0; padding:0;">Form Fields</h2>
                    <button type="button" class="btn btn-secondary" onclick="addField()">+ Add Field</button>
                </div>

                <div id="fields-container">
                    <!-- Fields will be injected here via JS -->
                </div>

                <input type="hidden" name="fields_json" id="fieldsJson">
            </div>

            <div style="text-align: right; margin-bottom: 3rem;">
                <button type="submit" class="btn">Save Tool</button>
            </div>
        </form>
    </div>

    <script>
        // Initialize fields from PHP if editing
        let fields = <?php echo isset($tool['fields']) ? json_encode($tool['fields']) : '[]'; ?>;

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
            container.innerHTML = '';

            fields.forEach((field, index) => {
                const div = document.createElement('div');
                div.className = 'field-item';

                // Use escapeHtml for user input values to prevent XSS and broken HTML
                const safeLabel = escapeHtml(field.label);
                const safeName = escapeHtml(field.field_name);
                const safeOptions = escapeHtml(Array.isArray(field.options) ? field.options.join(', ') : (field.options || ''));

                div.innerHTML = `
                    <div class="field-header">
                        <span>Field #${index + 1}</span>
                        <span class="remove-field" onclick="removeField(${index})">Remove</span>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label>Label</label>
                            <input type="text" value="${safeLabel}" oninput="updateField(${index}, 'label', this.value)" placeholder="e.g. Blog Topic">
                        </div>
                        <div class="col form-group">
                            <label>Variable Name</label>
                            <input type="text" value="${safeName}" oninput="updateField(${index}, 'field_name', this.value)" placeholder="e.g. topic">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                            <label>Type</label>
                            <select onchange="updateField(${index}, 'field_type', this.value)">
                                <option value="text" ${field.field_type === 'text' ? 'selected' : ''}>Short Text</option>
                                <option value="textarea" ${field.field_type === 'textarea' ? 'selected' : ''}>Long Text</option>
                                <option value="select" ${field.field_type === 'select' ? 'selected' : ''}>Dropdown Select</option>
                            </select>
                        </div>
                        <div class="col form-group" style="${field.field_type === 'select' ? '' : 'display:none;'}">
                            <label>Options (Comma separated)</label>
                            <input type="text" value="${safeOptions}" oninput="updateOptions(${index}, this.value)">
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });

            // Update hidden input
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
            if (confirm('Remove this field?')) {
                fields.splice(index, 1);
                renderFields();
            }
        }

        function updateField(index, key, value) {
            fields[index][key] = value;
            if (key === 'field_type') {
                renderFields(); // Only re-render if structure changes
            }
            updateHiddenInput();
        }

        function updateOptions(index, value) {
            // Split by comma and trim
            fields[index].options = value.split(',').map(s => s.trim());
            updateHiddenInput();
        }

        function updateHiddenInput() {
             document.getElementById('fieldsJson').value = JSON.stringify(fields);
        }

        function generateSlug(text) {
            // Only auto-generate if creating new tool
            <?php if (!isset($tool)): ?>
            const slug = text.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('slugInput').value = slug;
            <?php endif; ?>
        }

        // Initial Render
        renderFields();

        // Handle form submission serialization
        document.getElementById('toolForm').addEventListener('submit', function() {
            document.getElementById('fieldsJson').value = JSON.stringify(fields);
        });
    </script>
</body>
</html>
