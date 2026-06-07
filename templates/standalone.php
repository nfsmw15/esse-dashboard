<?php
/**
 * Standalone layout for guest-only pages (login, register, password reset, …).
 * No sidebar — minimal topbar, centered content, footer.
 *
 * @var array                $page
 * @var string               $content
 * @var string               $siteName
 * @var array                $footMenu
 * @var string               $iconPackCss
 * @var \EsseDashboard\Theme $theme
 */
$renderIcon = [$theme, 'renderIcon'];
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(($page['title'] ?? '') . ' — ' . $siteName) ?></title>
    <link rel="stylesheet" href="/public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($iconPackCss) ?>">
    <link rel="stylesheet" href="/public/vendor/esse-ui/esse-ui.css">
    <link rel="stylesheet" href="<?= $theme->assetUrl('css/esse-dashboard.css') ?>?v=20260605-iconpack-css">
    <script>
    (() => {
        const storedTheme = localStorage.getItem('esse-dashboard-theme');
        document.documentElement.setAttribute('data-bs-theme', storedTheme === 'dark' ? 'dark' : 'light');
    })();
    </script>
</head>
<body class="d-flex flex-column min-vh-100" style="background:var(--bg)">

<!-- Minimal topbar -->
<nav style="background:var(--sidebar-bg);border-bottom:1px solid var(--border);padding:.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between">
    <a href="/" class="text-decoration-none fw-bold" style="color:var(--heading)">
        <?= $renderIcon('grid-1x2-fill', 'me-2') ?><?= htmlspecialchars($siteName) ?>
    </a>
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="theme-icon-active"><?= $renderIcon('circle-half') ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-bs-theme-value="light"><?= $renderIcon('sun-fill') ?> Light</button></li>
            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-bs-theme-value="dark"><?= $renderIcon('moon-stars-fill') ?> Dark</button></li>
        </ul>
    </div>
</nav>

<!-- Content -->
<main class="flex-fill d-flex align-items-center justify-content-center px-3 py-5">
    <div style="width:100%;max-width:480px">
        <?php if (!empty($page['icon']) || !empty($page['title'])): ?>
        <h1 class="h4 text-center mb-4" style="color:var(--heading)">
            <?= $renderIcon($page['icon'] ?? null, 'me-2') ?>
            <?= htmlspecialchars($page['title'] ?? '') ?>
        </h1>
        <?php endif ?>
        <div class="esse-content">
            <?= $content ?>
        </div>
    </div>
</main>

<!-- Footer -->
<?php if ($footMenu):
    $groups = [];
    $current = ['header' => null, 'links' => []];
    foreach ($footMenu as $item) {
        if (($item['type'] ?? '') === 'header') {
            if ($current['header'] !== null || !empty($current['links'])) $groups[] = $current;
            $current = ['header' => $item['label'] ?? '', 'links' => $item['children'] ?? []];
        } else {
            $current['links'][] = $item;
        }
    }
    if ($current['header'] !== null || !empty($current['links'])) $groups[] = $current;
?>
<footer class="w-100 py-4" style="border-top:1px solid var(--border)">
    <div class="container d-flex flex-wrap justify-content-center gap-5" style="max-width:860px">
        <?php foreach ($groups as $group): ?>
        <div>
            <?php if ($group['header'] !== null): ?>
            <p class="small fw-semibold mb-1" style="color:var(--heading)"><?= htmlspecialchars($group['header'] ?? '') ?></p>
            <hr class="border-secondary mt-0 mb-2">
            <?php endif ?>
            <?php foreach ($group['links'] as $link): ?>
            <?php if (($link['type'] ?? '') === 'header'): ?>
            <p class="text-secondary small mb-1"><?= htmlspecialchars($link['label'] ?? '') ?></p>
            <?php else: ?>
            <div>
                <a href="<?= htmlspecialchars(\Esse\Menu::itemUrl($link)) ?>"
                   class="text-secondary small text-decoration-none"
                   <?= ($link['target'] ?? '') === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                    <?= htmlspecialchars($link['label'] ?? '') ?>
                </a>
            </div>
            <?php endif ?>
            <?php endforeach ?>
        </div>
        <?php endforeach ?>
    </div>
</footer>
<?php endif ?>

<script src="/public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const storedTheme = localStorage.getItem('esse-dashboard-theme') === 'dark' ? 'dark' : 'light';
    const setTheme = theme => {
        theme = theme === 'dark' ? 'dark' : 'light';
        localStorage.setItem('esse-dashboard-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.querySelectorAll('[data-bs-theme-value]').forEach(button => {
            button.classList.toggle('active', button.getAttribute('data-bs-theme-value') === theme);
        });
        const activeIcon = document.querySelector(`[data-bs-theme-value="${theme}"] i`);
        const themeIcon = document.querySelector('.theme-icon-active');
        if (activeIcon && themeIcon) themeIcon.innerHTML = activeIcon.outerHTML;
    };

    setTheme(storedTheme);
    document.querySelectorAll('[data-bs-theme-value]').forEach(button => {
        button.addEventListener('click', () => setTheme(button.getAttribute('data-bs-theme-value')));
    });
})();
</script>
</body>
</html>
