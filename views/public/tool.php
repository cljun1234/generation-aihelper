<?php include __DIR__ . '/../layouts/public_header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-5">

                    <div class="text-center mb-5">
                        <div class="tool-icon-wrapper mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                            <i class="fas <?= htmlspecialchars($tool['icon'] ?: 'fa-robot') ?>"></i>
                        </div>
                        <h2 class="fw-bold"><?= htmlspecialchars($tool['name']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($tool['description']) ?></p>
                    </div>

                    <form id="toolForm" onsubmit="submitTool(event)">
                        <input type="hidden" name="tool_id" value="<?= $tool['id'] ?>">

                        <?php foreach ($fields as $field): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold"><?= htmlspecialchars($field['label']) ?>
                                    <?php if ($field['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                </label>

                                <?php if ($field['type'] === 'textarea'): ?>
                                    <textarea name="<?= htmlspecialchars($field['variable_name']) ?>"
                                              class="form-control form-control-lg bg-light border-0"
                                              rows="4"
                                              placeholder="<?= htmlspecialchars($field['placeholder']) ?>"
                                              <?= $field['required'] ? 'required' : '' ?>></textarea>
                                <?php elseif ($field['type'] === 'select'): ?>
                                    <select name="<?= htmlspecialchars($field['variable_name']) ?>"
                                            class="form-select form-select-lg bg-light border-0"
                                            <?= $field['required'] ? 'required' : '' ?>>
                                        <option value="" disabled selected><?= htmlspecialchars($field['placeholder'] ?: 'Select an option') ?></option>
                                        <?php
                                            // Handle options (could be array or string)
                                            $options = is_array($field['options']) ? $field['options'] : explode(',', $field['options']);
                                            foreach ($options as $opt):
                                        ?>
                                            <option value="<?= htmlspecialchars(trim($opt)) ?>"><?= htmlspecialchars(trim($opt)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($field['variable_name']) ?>"
                                           class="form-control form-control-lg bg-light border-0"
                                           placeholder="<?= htmlspecialchars($field['placeholder']) ?>"
                                           <?= $field['required'] ? 'required' : '' ?>>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" id="submitBtn">
                                <i class="fas fa-magic me-2"></i> Generate Content
                            </button>
                        </div>
                    </form>

                    <!-- Result Area -->
                    <div id="resultArea" class="mt-5 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Generated Result</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyResult()">
                                <i class="far fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="bg-light p-4 rounded-3 border">
                            <pre id="resultContent" class="mb-0 text-wrap" style="font-family: inherit; font-size: 1rem; line-height: 1.6;"></pre>
                        </div>
                        <div class="d-grid mt-4">
                            <button class="btn btn-outline-primary" onclick="resetForm()">
                                <i class="fas fa-redo me-2"></i> Create Another
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="text-muted fw-bold">AI is thinking...</p>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="/gallery" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to Tools</a>
            </div>
        </div>
    </div>
</div>

<script>
async function submitTool(e) {
    e.preventDefault();

    const form = document.getElementById('toolForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');
    const loadingState = document.getElementById('loadingState');
    const resultArea = document.getElementById('resultArea');
    const resultContent = document.getElementById('resultContent');

    // UI Updates
    form.classList.add('d-none');
    loadingState.classList.remove('d-none');
    resultArea.classList.add('d-none'); // Ensure hidden

    try {
        const response = await fetch('/api/generate', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            resultContent.textContent = data.data;
            resultArea.classList.remove('d-none');
        } else {
            alert('Error: ' + (data.error || 'Unknown error occurred.'));
            form.classList.remove('d-none'); // Show form again
        }
    } catch (err) {
        alert('Network error. Please try again.');
        form.classList.remove('d-none');
    } finally {
        loadingState.classList.add('d-none');
    }
}

function resetForm() {
    document.getElementById('toolForm').reset();
    document.getElementById('toolForm').classList.remove('d-none');
    document.getElementById('resultArea').classList.add('d-none');
}

function copyResult() {
    const text = document.getElementById('resultContent').textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
}
</script>

<?php include __DIR__ . '/../layouts/public_footer.php'; ?>
