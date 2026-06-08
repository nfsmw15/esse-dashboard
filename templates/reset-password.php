<?php
/**
 * Theme-rendered /admin/reset-password page (auth.reset_password.render hook).
 * No sidebar — minimal topbar, centered card, footer.
 *
 * Auth logic (CSRF, token validation, password update) stays in
 * admin/reset-password.php — this template only renders the form.
 *
 * @var array                $data
 * @var array                $footMenu
 * @var string               $iconPackCss
 * @var \EsseDashboard\Theme $theme
 */
$renderIcon  = [$theme, 'renderIcon'];
$brandName   = $data['brandName']   ?? 'ESSE CMS';
$brandSlogan = $data['brandSlogan'] ?? '';
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars('Neues Passwort — ' . $brandName) ?></title>
    <link rel="stylesheet" href="/public/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($iconPackCss) ?>">
    <link rel="stylesheet" href="/public/vendor/esse-ui/esse-ui.css">
    <link rel="stylesheet" href="<?= $theme->assetUrl('css/esse-dashboard.css') ?>?v=20260608-inline-js-light-card">
    <script src="<?= $theme->assetUrl('js/theme-init.js') ?>?v=20260608-inline-js-light-card"></script>
</head>
<body class="d-flex flex-column min-vh-100" style="background:var(--bg)">

<!-- Minimal topbar -->
<nav style="background:var(--sidebar-bg);border-bottom:1px solid var(--border);padding:.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between">
    <a href="/" class="text-decoration-none fw-bold" style="color:var(--heading)">
        <?= $renderIcon('grid-1x2-fill', 'me-2') ?><?= htmlspecialchars($brandName) ?>
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
    <div style="width:100%;max-width:440px">
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold mb-0" style="color:var(--heading)">
                <?= $renderIcon('grid-1x2-fill', 'me-2') ?><?= htmlspecialchars($brandName) ?>
            </h1>
            <?php if ($brandSlogan !== ''): ?>
            <small class="text-secondary d-block"><?= htmlspecialchars($brandSlogan) ?></small>
            <?php endif ?>
            <small class="text-secondary">Neues Passwort setzen</small>
        </div>

        <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success">
            Passwort erfolgreich geändert. Du kannst dich jetzt anmelden.
        </div>
        <a href="/login" class="btn btn-primary w-100">Zum Login</a>

        <?php elseif (empty($data['valid'])): ?>
        <div class="alert alert-danger">
            Dieser Link ist ungültig oder abgelaufen.
        </div>
        <a href="/admin/forgot-password" class="btn btn-outline-secondary w-100">
            Neuen Link anfordern
        </a>

        <?php else: ?>

        <?php if (!empty($data['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($data['errors'] as $error): ?><div><?= htmlspecialchars($error) ?></div><?php endforeach ?>
        </div>
        <?php endif ?>

        <div class="dash-card esse-content esse-content--standalone">
            <form method="post">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($data['csrfToken'] ?? '') ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">Neues Passwort</label>
                    <input type="password" name="password" class="form-control"
                           autocomplete="new-password" autofocus required>
                    <div class="form-text">Mindestens 10 Zeichen</div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Passwort bestätigen</label>
                    <input type="password" name="password_confirm" class="form-control"
                           autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Passwort speichern</button>
            </form>
        </div>
        <?php endif ?>
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
<script src="<?= $theme->assetUrl('js/esse-dashboard.js') ?>?v=20260608-inline-js-light-card"></script>
</body>
</html>
