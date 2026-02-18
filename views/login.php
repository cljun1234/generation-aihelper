<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tools - Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 350px; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0b5ed7; }
        h2 { text-align: center; margin-top: 0; color: #333; }
        .hint { font-size: 0.8rem; color: #666; text-align: center; margin-top: 15px; background: #e9ecef; padding: 10px; border-radius: 4px;}
    </style>
</head>
<body>
    <div class="login-box">
        <div style="text-align: center; margin-bottom: 20px; font-size: 40px; color: #0d6efd;">
            <i class="fas fa-brain"></i>
        </div>
        <h2>AI Tools Admin</h2>
        <form action="/login" method="POST">
            <?php echo CSRF::field(); ?>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="hint">Demo: admin@example.com / password</p>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>
