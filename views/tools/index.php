<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FormWise Clone</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Dashboard Specific Overrides */

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            z-index: 1000;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-item {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }
        .nav-item:hover, .nav-item.active {
            background-color: #EEF2FF; /* Light Indigo */
            color: var(--primary-blue);
        }
        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }

        /* Search Bar */
        .search-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .search-wrapper input {
            padding-left: 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            background-color: #F9FAFB;
        }
        .search-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        /* Promo Cards in Sidebar */
        .promo-card {
            background: linear-gradient(135deg, #2b2d42 0%, #1a1b26 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Tool Cards */
        .tool-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        .tool-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .tool-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: #EEF2FF;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .tool-tag {
            background: #F3F4F6;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .tool-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            min-height: 3rem; /* Ensure consistent height for titles */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 1.1rem;
        }
        .tool-stats {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        /* Match FormWise Gradient for AI Tool Builder */
        .btn-purple-gradient {
            background: linear-gradient(90deg, #7C3AED 0%, #C026D3 100%); /* Purple to Pink/Fuchsia */
            color: white;
            border: none;
            font-weight: 500;
        }
        .btn-purple-gradient:hover {
            background: linear-gradient(90deg, #6D28D9 0%, #A21CAF 100%);
            color: white;
        }
        .btn-blue {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            font-weight: 500;
        }
        .btn-blue:hover {
            background-color: #4338CA;
            color: white;
        }

        /* Context Menu */
        .card-menu {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: var(--text-secondary);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="/" class="logo text-decoration-none">
            FormWise
        </a>

        <div class="mb-3">
            <select class="form-select form-select-sm border bg-light text-secondary">
                <option>All Tools</option>
            </select>
        </div>

        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="form-control form-control-sm" placeholder="Search Tools">
        </div>

        <nav class="nav flex-column mb-4">
            <a href="/" class="nav-item active">
                <i class="fa-solid fa-grip"></i> Dashboard
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-layer-group"></i> Toolsets
            </a>
            <a href="#" class="nav-item">
                <i class="fa-brands fa-facebook"></i> Facebook Group
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-circle-info"></i> Resources Center
            </a>
        </nav>

        <div class="mt-auto">
            <div class="promo-card">
                <i class="fa-solid fa-bullhorn fa-lg me-2"></i>
                <div>
                    <strong>Platform Updates</strong>
                </div>
            </div>

            <div class="promo-card" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);">
                <i class="fa-solid fa-user-shield fa-lg me-2"></i>
                <div>
                    <strong>Start earning with LoginMode</strong>
                </div>
            </div>

            <div class="sidebar-footer d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                    I
                </div>
                <div class="flex-grow-1" style="font-size: 0.9rem;">
                    <div class="fw-bold text-dark">Info T</div>
                </div>
                <i class="fa-solid fa-chevron-up text-muted" style="font-size: 0.8rem;"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="mb-0 fw-bold text-dark">🌙 Welcome, Night Owl!</h3>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-sm fw-medium">
                    <i class="fa-solid fa-cubes"></i> Starter Packs
                </button>
                <button class="btn btn-purple-gradient btn-sm position-relative shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Tool Builder
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.6rem; margin-top: -5px; margin-left: -10px;">
                        NEW
                    </span>
                </button>
                <a href="/tools/create" class="btn btn-blue btn-sm shadow-sm">
                    <i class="fa-solid fa-plus"></i> Create a New Tool
                </a>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 text-secondary fw-semibold"><i class="fa-solid fa-briefcase me-2"></i> Your tools</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small fw-medium">Sort by</span>
                <select class="form-select form-select-sm border-0 bg-transparent text-dark fw-bold" style="width: auto; cursor: pointer;">
                    <option>Last Modified</option>
                    <option>Name</option>
                    <option>Date Created</option>
                </select>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($tools)): ?>
                <?php foreach($tools as $tool): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="tool-card">
                        <div class="card-menu">
                             <div class="dropdown">
                                <i class="fa-regular fa-circle-question fa-lg" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/tools/edit/<?php echo $tool['id']; ?>">Edit</a></li>
                                    <li><a class="dropdown-item" href="/tool/<?php echo $tool['slug']; ?>" target="_blank">View Public</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="/tools/delete/<?php echo $tool['id']; ?>" method="POST" onsubmit="return confirm('Delete this tool?');">
                                            <?php echo CSRF::field(); ?>
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="tool-tag">
                                <i class="fa-solid fa-file-lines text-secondary"></i> SmartForm
                            </div>
                            <div class="text-secondary small" style="font-size: 0.75rem;">
                                Date Modified: <?php echo date('M j, Y', strtotime($tool['created_at'])); ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <div class="tool-icon flex-shrink-0">
                                <!-- Placeholder icon, maybe could be customizable later -->
                                <i class="fa-solid fa-file-pen"></i>
                            </div>
                            <div style="flex-grow: 1;">
                                <h6 class="tool-title">
                                    <a href="/tools/edit/<?php echo $tool['id']; ?>" class="text-decoration-none text-dark stretched-link">
                                        <?php echo htmlspecialchars($tool['title']); ?>
                                    </a>
                                </h6>
                            </div>
                        </div>

                        <div class="tool-stats pt-3 mt-3 border-top">
                            0 Responses
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 text-secondary">
                    <i class="fa-solid fa-box-open fa-3x mb-3 text-muted"></i>
                    <p class="fs-5">No tools created yet.</p>
                    <a href="/tools/create" class="btn btn-blue btn-sm mt-2">Create your first tool</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Chat Button -->
    <div style="position: fixed; bottom: 2rem; right: 2rem; z-index: 1050;">
        <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #2563EB;">
            <i class="fa-solid fa-comment-dots fa-lg text-white"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
