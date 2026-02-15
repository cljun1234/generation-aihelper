<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - AI Tools</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        header { background: white; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: bold; font-size: 1.2rem; color: #1a73e8; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #555; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { padding: 10px 20px; background: #1a73e8; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
    </style>
</head>
<body>
    <header>
        <div class="logo">AI Tools</div>
        <div class="nav-links">
            <a href="/">Dashboard</a>
            <a href="/tools">My Tools</a>
            <a href="/settings" style="color:#1a73e8; font-weight:bold;">Settings</a>
            <a href="/logout">Logout</a>
        </div>
    </header>

    <div class="container">
        <form action="/settings/save" method="POST">

            <div class="card">
                <h2>API Configuration</h2>
                <p style="color:#666; font-size:0.9rem;">
                    Enter your API keys to enable AI generation. These are stored securely.
                </p>

                <div class="form-group">
                    <label>OpenAI API Key</label>
                    <input type="password" name="openai_key" value="<?php echo htmlspecialchars($apiSettings['openai_key'] ?? ''); ?>" placeholder="sk-...">
                </div>

                <div class="form-group">
                    <label>DeepSeek API Key</label>
                    <input type="password" name="deepseek_key" value="<?php echo htmlspecialchars($apiSettings['deepseek_key'] ?? ''); ?>" placeholder="sk-...">
                </div>
            </div>

            <div class="card">
                <h2>General Settings</h2>
                <div class="form-group">
                    <label>Custom Domain (Optional)</label>
                    <input type="text" name="domain" value="<?php echo htmlspecialchars($domain['domain'] ?? ''); ?>" placeholder="example.com">
                </div>
            </div>

            <button type="submit" class="btn">Save All Settings</button>
        </form>
    </div>
</body>
</html>
