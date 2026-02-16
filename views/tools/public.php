<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tool['title']); ?> - AI Tool</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Public View Specific Overrides */
        body {
            background-color: var(--body-bg);
            display: flex;
            justify-content: center;
            align-items: center; /* Center vertically */
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .tool-card {
            background: white;
            border-radius: 1rem; /* More rounded */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Tailwind-like shadow */
            width: 100%;
            max-width: 650px; /* Slightly wider */
            overflow: hidden;
            border: 1px solid #F3F4F6; /* Very light border */
        }

        .card-header {
            padding: 2.5rem 2rem 1.5rem;
            background: white;
            border-bottom: none;
            text-align: center;
        }

        .tool-icon {
            width: 72px;
            height: 72px;
            background: #EEF2FF; /* Indigo 50 */
            color: var(--primary-blue);
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1.25rem;
        }

        .tool-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827; /* Gray 900 */
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        .tool-description {
            color: #6B7280; /* Gray 500 */
            font-size: 1rem;
            line-height: 1.5;
            max-width: 80%;
            margin: 0 auto;
        }

        .card-body {
            padding: 1rem 2.5rem 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151; /* Gray 700 */
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .form-control, .form-select {
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            border-color: #D1D5DB; /* Gray 300 */
            font-size: 1rem;
            background-color: #F9FAFB; /* Gray 50 input bg */
        }

        .form-control:focus, .form-select:focus {
            background-color: white;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); /* Bigger focus ring */
        }

        /* Generate Button */
        .btn-generate {
            background: linear-gradient(to right, #4F46E5, #4338CA); /* Subtle gradient */
            color: white;
            font-weight: 600;
            padding: 1rem;
            border-radius: 0.5rem;
            width: 100%;
            border: none;
            font-size: 1.1rem;
            transition: all 0.2s;
            margin-top: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);
        }

        .btn-generate:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.5);
        }

        .btn-generate:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Result Area */
        #result-area {
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: #F8FAFC; /* Slate 50 */
            border-radius: 0.75rem;
            border: 1px solid #E2E8F0; /* Slate 200 */
            display: none;
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .result-title {
            font-weight: 600;
            color: #1E293B; /* Slate 800 */
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .copy-btn {
            background: white;
            border: 1px solid #CBD5E1;
            color: #475569;
            padding: 0.4rem 0.8rem;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .copy-btn:hover {
            background: #F1F5F9;
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        #result-content {
            font-family: 'Inter', sans-serif;
            line-height: 1.7;
            color: #334155; /* Slate 700 */
            white-space: pre-wrap;
            font-size: 1rem;
        }

        /* Loading */
        .loading-overlay {
            display: none;
            text-align: center;
            padding: 3rem 1rem;
        }
        .spinner {
            width: 3.5rem;
            height: 3.5rem;
            border: 4px solid #E5E7EB;
            border-top-color: var(--primary-blue);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>

    <div class="tool-card">
        <div class="card-header">
            <div class="tool-icon shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <h1 class="tool-title"><?php echo htmlspecialchars($tool['title']); ?></h1>
            <p class="tool-description">
                <?php echo !empty($tool['system_prompt']) ? 'Ready to generate content tailored to your needs.' : 'Fill out the form below.'; ?>
            </p>
        </div>

        <div class="card-body">
            <form id="tool-form">
                <?php echo CSRF::field(); ?>
                <div class="vstack gap-4">
                    <?php
                    $fields = isset($tool['fields']) ? $tool['fields'] : [];
                    if (is_string($fields)) $fields = json_decode($fields, true);

                    if (!empty($fields)):
                        foreach($fields as $field):
                    ?>
                    <div>
                        <label class="form-label" for="<?php echo htmlspecialchars($field['field_name']); ?>">
                            <?php echo htmlspecialchars($field['label']); ?>
                        </label>

                        <?php if ($field['field_type'] === 'textarea'): ?>
                            <textarea class="form-control shadow-sm" name="<?php echo htmlspecialchars($field['field_name']); ?>" rows="4" required placeholder="Enter <?php echo strtolower($field['label']); ?>..."></textarea>

                        <?php elseif ($field['field_type'] === 'select'): ?>
                            <select class="form-select shadow-sm" name="<?php echo htmlspecialchars($field['field_name']); ?>">
                                <?php
                                $options = $field['options'];
                                if (is_string($options)) $options = json_decode($options, true);
                                if (is_array($options)):
                                    foreach($options as $opt): ?>
                                    <option value="<?php echo htmlspecialchars(trim($opt)); ?>"><?php echo htmlspecialchars(trim($opt)); ?></option>
                                    <?php endforeach;
                                endif; ?>
                            </select>

                        <?php else: ?>
                            <input type="text" class="form-control shadow-sm" name="<?php echo htmlspecialchars($field['field_name']); ?>" required placeholder="Enter <?php echo strtolower($field['label']); ?>...">
                        <?php endif; ?>
                    </div>
                    <?php endforeach;
                    else: ?>
                        <div class="alert alert-info border-0 bg-light text-center">
                            <i class="fa-solid fa-info-circle me-2"></i> No input fields configured for this tool. Click generate to proceed.
                        </div>
                    <?php endif; ?>

                    <button type="submit" id="submit-btn" class="btn-generate">
                        <i class="fa-solid fa-bolt me-2"></i> Generate AI Content
                    </button>
                </div>
            </form>

            <div class="loading-overlay" id="loading-indicator">
                <div class="spinner"></div>
                <h5 class="fw-bold text-dark mb-1">Generating Magic...</h5>
                <p class="text-muted small">This may take a few seconds.</p>
            </div>

            <div id="result-area">
                <div class="result-header">
                    <h3 class="result-title">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        Result
                    </h3>
                    <button class="copy-btn" onclick="copyResult()">
                        <i class="fa-regular fa-copy me-1"></i> Copy Text
                    </button>
                </div>
                <div id="result-content"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tool-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('submit-btn');
            const form = document.getElementById('tool-form');
            const loading = document.getElementById('loading-indicator');
            const resultArea = document.getElementById('result-area');
            const resultContent = document.getElementById('result-content');

            // Reset UI
            btn.style.display = 'none';
            form.style.opacity = '0.4';
            form.style.pointerEvents = 'none';

            loading.style.display = 'block';
            resultArea.style.display = 'none';
            resultContent.textContent = '';

            const formData = new FormData(this);

            fetch('/tool/process/<?php echo $tool['id']; ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                btn.style.display = 'block';
                form.style.opacity = '1';
                form.style.pointerEvents = 'auto';

                if (data.success) {
                    resultArea.style.display = 'block';
                    resultContent.textContent = data.data;
                    // Scroll to result
                    resultArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    alert('Error: ' + (data.error || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                btn.style.display = 'block';
                form.style.opacity = '1';
                form.style.pointerEvents = 'auto';
                alert('Network Error: ' + error.message);
            });
        });

        function copyResult() {
            const content = document.getElementById('result-content').textContent;
            navigator.clipboard.writeText(content).then(() => {
                const btn = document.querySelector('.copy-btn');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
                btn.classList.add('text-success', 'border-success');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('text-success', 'border-success');
                }, 2000);
            });
        }
    </script>
</body>
</html>
