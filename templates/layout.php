<?php
/**
 * @var array               $page
 * @var string              $content
 * @var string              $siteName
 * @var array               $sidebarMenu
 * @var array               $footMenu
 * @var \EsseDashboard\Theme $theme
 */

$currentSlug = $page['slug'] ?? '';
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page['title'] . ' — ' . $siteName) ?></title>
    <link rel="stylesheet" href="/public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/public/vendor/esse-ui/esse-ui.css">
    <link rel="stylesheet" href="<?= $theme->assetUrl('css/esse-dashboard.css') ?>?v=20260604-scrollbar-edge">
    <script>
    (() => {
        const storedTheme = localStorage.getItem('esse-dashboard-theme');
        document.documentElement.setAttribute('data-bs-theme', storedTheme === 'dark' ? 'dark' : 'light');
    })();
    </script>
</head>
<body>

<!-- Mobile overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- ── Sidebar ── -->
<div id="dash-sidebar">
    <div class="brand">
        <a href="/" class="text-decoration-none">
            <i class="bi bi-grid-1x2-fill me-2"></i><?= htmlspecialchars($siteName) ?>
        </a>
        <small>forge your web.</small>
    </div>

    <nav class="nav flex-column">
        <?php foreach ($sidebarMenu as $item):
            $url        = \Esse\Menu::itemUrl($item);
            $hasChildren = !empty($item['children']);
            $isActive   = $currentSlug === ltrim($url, '/');
            if ($hasChildren) {
                foreach ($item['children'] as $child) {
                    if ($child['type'] !== 'header' && $currentSlug === ltrim(\Esse\Menu::itemUrl($child), '/')) {
                        $isActive = true;
                        break;
                    }
                }
            }
        ?>

        <?php if ($item['type'] === 'header'): ?>
            <div class="nav-section"><?= htmlspecialchars($item['label']) ?></div>

        <?php elseif ($hasChildren): ?>
            <div class="dash-has-children <?= $isActive ? 'open' : '' ?>">
                <a href="<?= htmlspecialchars($url) ?>"
                   class="nav-link <?= $isActive ? 'active' : '' ?>"
                   onclick="toggleSubMenu(event, this.parentElement)"
                   <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                    <?php if (!empty($item['icon'])): ?><i class="<?= htmlspecialchars($item['icon']) ?>"></i><?php endif ?>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span class="dash-arrow"><i class="bi bi-chevron-right"></i></span>
                </a>
                <div class="dash-sub">
                    <?php foreach ($item['children'] as $child): ?>
                    <?php if ($child['type'] === 'header'): ?>
                        <div class="nav-section"><?= htmlspecialchars($child['label']) ?></div>
                    <?php else: ?>
                        <?php $childUrl = \Esse\Menu::itemUrl($child); ?>
                        <a href="<?= htmlspecialchars($childUrl) ?>"
                           class="nav-link <?= $currentSlug === ltrim($childUrl, '/') ? 'active' : '' ?>"
                           <?= $child['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                            <?php if (!empty($child['icon'])): ?><i class="<?= htmlspecialchars($child['icon']) ?>"></i><?php endif ?>
                            <span><?= htmlspecialchars($child['label']) ?></span>
                        </a>
                    <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>

        <?php else: ?>
            <a href="<?= htmlspecialchars($url) ?>"
               class="nav-link <?= $isActive ? 'active' : '' ?>"
               <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                <?php if (!empty($item['icon'])): ?><i class="<?= htmlspecialchars($item['icon']) ?>"></i><?php endif ?>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endif ?>

        <?php endforeach ?>
    </nav>

    <!-- Sidebar footer: user info or footer links -->
    <div class="sidebar-footer">
        <?php if (\Esse\Auth::check()): ?>
        <div class="dropdown">
            <button class="btn user-menu dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
                <span><?= htmlspecialchars(\Esse\Auth::user()['display_name'] ?? '') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow w-100">
                <li><a class="dropdown-item" href="/profil"><i class="bi bi-gear me-2"></i>Profil</a></li>
                <?php if (\Esse\Auth::meetsRole('author')): ?>
                <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Admin</a></li>
                <?php endif ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="post" action="/abmelden">
                        <input type="hidden" name="_csrf" value="<?= \Esse\Auth::csrfToken() ?>">
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Abmelden</button>
                    </form>
                </li>
            </ul>
        </div>
        <?php else: ?>
        <a href="/admin/login" class="text-secondary small text-decoration-none">
            <i class="bi bi-person me-1"></i>Anmelden
        </a>
        <?php endif ?>
    </div>
</div>

<!-- ── Topbar ── -->
<div id="dash-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link p-0 text-secondary d-lg-none" onclick="toggleSidebar()" style="font-size:1.2rem">
            <i class="bi bi-list"></i>
        </button>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-circle-half theme-icon-active"></i>
                <span class="d-none d-sm-inline ms-1">Theme</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-bs-theme-value="light"><i class="bi bi-sun-fill"></i> Light</button></li>
                <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-bs-theme-value="dark"><i class="bi bi-moon-stars-fill"></i> Dark</button></li>
            </ul>
        </div>
    </div>
</div>

<!-- ── Content ── -->
<div id="dash-main">
    <div class="dash-content">
        <header class="dash-content-header">
            <h1>
                <?php if (!empty($page['icon'])): ?><i class="<?= htmlspecialchars($page['icon']) ?> me-2"></i><?php endif ?>
                <?= htmlspecialchars($page['title']) ?>
            </h1>
        </header>
        <div class="esse-content">
            <?= $content ?>
        </div>
    </div>

    <?php if ($footMenu): ?>
    <footer class="dash-footer">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span class="text-secondary small">&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?></span>
            <?php foreach ($footMenu as $item): ?>
            <?php if ($item['type'] !== 'header'): ?>
            <a href="<?= htmlspecialchars(\Esse\Menu::itemUrl($item)) ?>"
               class="text-secondary small text-decoration-none"
               <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                <?= htmlspecialchars($item['label']) ?>
            </a>
            <?php endif ?>
            <?php endforeach ?>
        </div>
    </footer>
    <?php endif ?>
</div>

<script src="/public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('dash-sidebar').classList.toggle('open');
    document.getElementById('sidebar-overlay').classList.toggle('show');
}

function toggleSubMenu(e, el) {
    e.preventDefault();
    el.classList.toggle('open');
}

document.querySelectorAll('.dash-has-children').forEach(el => {
    if (el.querySelector('.dash-sub a.active')) {
        el.classList.add('open');
    }
});

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
        if (activeIcon && themeIcon) themeIcon.className = activeIcon.className + ' theme-icon-active';
    };

    setTheme(storedTheme);
    document.querySelectorAll('[data-bs-theme-value]').forEach(button => {
        button.addEventListener('click', () => setTheme(button.getAttribute('data-bs-theme-value')));
    });
})();
</script>
</body>
</html>
