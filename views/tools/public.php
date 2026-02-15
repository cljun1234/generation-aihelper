<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tool['title']); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #ffffff; margin: 0; padding: 0; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        h1 { text-align: center; margin-bottom: 2rem; font-size: 1.8rem; color: #1a73e8; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 8px; font-weight: 500; }
        input[type="text"], textarea, select { width: 100%; padding: 12px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; transition: border-color 0.2s; }
        input[type="text"]:focus, textarea:focus, select:focus { border-color: #1a73e8; outline: none; }
        textarea { min-height: 120px; resize: vertical; }
        .btn { display: block; width: 100%; padding: 14px; background: #1a73e8; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1.1rem; font-weight: bold; transition: background 0.2s; }
        .btn:hover { background: #1557b0; }
        .btn:disabled { background: #ccc; cursor: not-allowed; }

        #result-area { margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; display: none; }
        #result-content { line-height: 1.6; white-space: pre-wrap; }
        .loading { text-align: center; color: #666; display: none; margin-top: 1rem; }

        /* Loading Spinner */
        .spinner { display: inline-block; width: 24px; height: 24px; border: 3px solid rgba(0,0,0,0.1); border-radius: 50%; border-top-color: #1a73e8; animation: spin 1s ease-in-out infinite; vertical-align: middle; margin-right: 10px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($tool['title']); ?></h1>

        <form id="tool-form">
            <?php foreach($fields as $field): ?>
            <div class="form-group">
                <label for="<?php echo htmlspecialchars($field['field_name']); ?>"><?php echo htmlspecialchars($field['label']); ?></label>

                <?php if ($field['field_type'] === 'textarea'): ?>
                    <textarea name="<?php echo htmlspecialchars($field['field_name']); ?>" required></textarea>

                <?php elseif ($field['field_type'] === 'select'): ?>
                    <select name="<?php echo htmlspecialchars($field['field_name']); ?>">
                        <?php
                        $options = $field['options']; // Already decoded in controller
                        if (is_string($options)) $options = json_decode($options, true); // Fallback
                        if (is_array($options)):
                            foreach($options as $opt): ?>
                            <option value="<?php echo htmlspecialchars(trim($opt)); ?>"><?php echo htmlspecialchars(trim($opt)); ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>

                <?php else: ?>
                    <input type="text" name="<?php echo htmlspecialchars($field['field_name']); ?>" required>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <button type="submit" id="submit-btn">Generate AI Response</button>
            <div class="loading" id="loading-indicator">
                <div class="spinner"></div> Generating... please wait.
            </div>
        </form>

        <div id="result-area">
            <h3 style="margin-top:0;">Result:</h3>
            <div id="result-content"></div>
        </div>
    </div>

    <script>
        document.getElementById('tool-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('submit-btn');
            const loading = document.getElementById('loading-indicator');
            const resultArea = document.getElementById('result-area');
            const resultContent = document.getElementById('result-content');

            // Reset UI
            btn.disabled = true;
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
                btn.disabled = false;

                if (data.success) {
                    resultArea.style.display = 'block';
                    resultContent.textContent = data.data;
                } else {
                    alert('Error: ' + (data.error || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                btn.disabled = false;
                alert('Network Error: ' + error.message);
            });
        });
    </script>
</body>
</html>
