<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tools - Dashboard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        header { background: white; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: bold; font-size: 1.2rem; color: #1a73e8; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #555; }
        .nav-links a.active { color: #1a73e8; font-weight: bold; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 16px; background: #1a73e8; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .btn-green { background: #28a745; }
        .btn-sm { padding: 4px 8px; font-size: 0.8rem; background: #dc3545; }
        .btn-edit { padding: 4px 8px; font-size: 0.8rem; background: #ffc107; color: #333; margin-right: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        th { color: #666; font-size: 0.9rem; }
        .badge { background: #e8f0fe; color: #1a73e8; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <header>
        <div class="logo">AI Tools</div>
        <div class="nav-links">
            <a href="/">Dashboard</a>
            <a href="/tools" class="active">My Tools</a>
            <a href="/settings">Settings</a>
            <a href="/logout">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>
                My AI Tools
                <a href="/tools/create" class="btn btn-green">Create New Tool</a>
            </h2>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Model</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tools)): ?>
                        <?php foreach($tools as $tool): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tool['title']); ?></td>
                            <td>
                                <a href="/tool/<?php echo htmlspecialchars($tool['slug']); ?>" target="_blank">
                                    /tool/<?php echo htmlspecialchars($tool['slug']); ?>
                                </a>
                            </td>
                            <td><span class="badge"><?php echo htmlspecialchars($tool['model_provider']); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($tool['created_at'])); ?></td>
                            <td style="text-align: right;">
                                <a href="/tools/edit/<?php echo $tool['id']; ?>" class="btn btn-edit">Edit</a>
                                <form action="/tools/delete/<?php echo $tool['id']; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    <button type="submit" class="btn btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; padding: 2rem;">
                                No tools created yet. Click "Create New Tool" to get started.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
