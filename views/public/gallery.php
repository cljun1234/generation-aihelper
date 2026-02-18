<?php include __DIR__ . '/../layouts/public_header.php'; ?>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <div class="d-flex align-items-center mb-5 px-2">
        <!-- Logo or Branding -->
        <i class="fas fa-cube fa-2x text-primary me-2"></i>
        <span class="fw-bold fs-5">AI Tools</span>
    </div>

    <nav class="nav flex-column">
        <a href="#" class="category-link active" onclick="filterCategory('all', this)">
            <i class="fas fa-layer-group me-2"></i> ALL
        </a>
        <?php foreach (array_keys($categories) as $catName): ?>
            <a href="#" class="category-link" onclick="filterCategory('<?= htmlspecialchars($catName) ?>', this)">
                <i class="far fa-folder me-2"></i> <?= htmlspecialchars($catName) ?>
            </a>
        <?php endforeach; ?>
        <?php if (!empty($uncategorized)): ?>
            <a href="#" class="category-link" onclick="filterCategory('Uncategorized', this)">
                <i class="far fa-folder me-2"></i> Uncategorized
            </a>
        <?php endif; ?>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h4 class="fw-bold mb-0" id="current-category-title">ALL</h4>
        <div class="position-relative">
            <input type="text" class="search-bar" placeholder="Search..." onkeyup="searchTools(this.value)">
            <i class="fas fa-search position-absolute text-muted" style="right: 15px; top: 12px;"></i>
        </div>
    </div>

    <div class="row g-4" id="tools-grid">
        <!-- Loop through ALL tools -->
        <?php foreach ($tools as $tool): ?>
            <div class="col-md-6 col-lg-4 tool-item"
                 data-category="<?= htmlspecialchars($tool['category_name'] ?: 'Uncategorized') ?>"
                 data-name="<?= htmlspecialchars(strtolower($tool['name'])) ?>">

                <a href="/tool/<?= $tool['slug'] ?>" class="text-decoration-none text-dark">
                    <div class="tool-card p-4 h-100">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="tool-icon-wrapper">
                                <i class="fas <?= htmlspecialchars($tool['icon'] ?: 'fa-robot') ?>"></i>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2 align-self-start">
                                <i class="fas fa-magic me-1 text-warning"></i> SmartForm
                            </span>
                        </div>

                        <h5 class="fw-bold mb-2"><?= htmlspecialchars($tool['name']) ?></h5>
                        <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($tool['description'] ?: 'No description.') ?>
                        </p>
                    </div>
                </a>

            </div>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="text-center py-5 d-none">
        <div class="text-muted mb-3"><i class="fas fa-search fa-3x"></i></div>
        <h5>No tools found</h5>
    </div>
</div>

<script>
function filterCategory(category, element) {
    // Update active link
    document.querySelectorAll('.category-link').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    // Update title
    document.getElementById('current-category-title').innerText = category === 'all' ? 'ALL' : category;

    // Filter items
    const tools = document.querySelectorAll('.tool-item');
    let visibleCount = 0;

    tools.forEach(tool => {
        const toolCat = tool.getAttribute('data-category');
        if (category === 'all' || toolCat === category) {
            tool.classList.remove('d-none');
            visibleCount++;
        } else {
            tool.classList.add('d-none');
        }
    });

    checkEmpty(visibleCount);
}

function searchTools(query) {
    const tools = document.querySelectorAll('.tool-item');
    const term = query.toLowerCase();
    let visibleCount = 0;

    // Determine current category filter (from active link)
    const activeLink = document.querySelector('.category-link.active');
    // If we want search to respect category, we need to know the category logic.
    // For simplicity, search overrides category or works within "ALL".
    // Let's make search work across ALL tools regardless of category tab,
    // OR search within the visible set.
    // Usually users expect search to find anything.
    // So let's reset category to ALL visual indicator if searching?
    // Or just search within current filter.
    // Let's search globally for better UX.

    tools.forEach(tool => {
        const name = tool.getAttribute('data-name');
        if (name.includes(term)) {
             tool.classList.remove('d-none');
             visibleCount++;
        } else {
             tool.classList.add('d-none');
        }
    });

    checkEmpty(visibleCount);
}

function checkEmpty(count) {
    const noResults = document.getElementById('no-results');
    if (count === 0) {
        noResults.classList.remove('d-none');
    } else {
        noResults.classList.add('d-none');
    }
}
</script>

<?php include __DIR__ . '/../layouts/public_footer.php'; ?>
