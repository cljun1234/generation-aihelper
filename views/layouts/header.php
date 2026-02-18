<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tools Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: #495057;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f1f3f5;
            color: #0d6efd;
        }
        .nav-link i {
            width: 24px;
        }
        .tool-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .tool-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .tool-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 24px;
            color: #0d6efd;
            margin-bottom: 15px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .status-draft { background: #e9ecef; color: #495057; }
        .status-published { background: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column">
    <a href="/" class="d-flex align-items-center mb-4 text-decoration-none text-dark">
        <i class="fas fa-brain fa-2x me-2 text-primary"></i>
        <span class="fs-4 fw-bold">AI Tools</span>
    </a>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/dashboard" class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/dashboard' || $_SERVER['REQUEST_URI'] == '/') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/tools" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/tools') === 0) ? 'active' : '' ?>">
                <i class="fas fa-tools"></i> Tools
            </a>
        </li>
        <li>
            <a href="/kb" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/kb') === 0) ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Knowledge Base
            </a>
        </li>
        <li>
            <a href="/settings" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/settings') === 0) ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    </ul>

    <div class="mt-auto pt-3 border-top">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <strong>Admin User</strong>
            </div>
            <a href="/logout" class="text-danger" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</div>

<div class="main-content">
