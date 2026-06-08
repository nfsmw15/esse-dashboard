<?php
/**
 * Theme-rendered /login page (auth.login.render hook).
 * No sidebar — minimal topbar, centered card, footer.
 *
 * Auth logic (CSRF, rate-limiting, Auth::attempt(), redirect resolution)
 * stays in admin/login.php — this template only renders the form.
 *
 * @var array                $data
 * @var string               $iconPackCss
 * @var \EsseDashboard\Theme $theme
 */
$renderIcon  = [$theme, 'renderIcon'];
$brandName   = $data['brandName']   ?? 'ESSE CMS';
$brandSlogan = $data['brandSlogan'] ?? '';
$footMenu    = $data['footMenu']    ?? [];
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars('Login — ' . $brandName) ?></title>
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
            <small class="text-secondary"><?= htmlspecialchars($brandSlogan) ?></small>
            <?php endif ?>
        </div>

        <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif ?>

        <div class="dash-card esse-content esse-content--standalone">
            <form method="post" action="/login">
                <input type="hidden" name="_csrf"    value="<?= htmlspecialchars($data['csrfToken'] ?? '') ?>">
                <input type="hidden" name="_form"    value="admin_login">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($data['redirect'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">E-Mail</label>
                    <input type="email" name="login" class="form-control" autocomplete="username" autofocus required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Passwort</label>
                    <input type="password" name="password" class="form-control" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Anmelden</button>
            </form>

            <div class="d-none mt-3" id="passkey-login-block">
                <div class="d-flex align-items-center my-3">
                    <hr class="border-secondary flex-grow-1 my-0">
                    <span class="text-secondary small mx-2">oder</span>
                    <hr class="border-secondary flex-grow-1 my-0">
                </div>
                <button type="button" id="passkey-login-btn" class="btn btn-outline-secondary w-100"
                        data-csrf-token="<?= htmlspecialchars($data['csrfToken'] ?? '') ?>"
                        data-redirect="<?= htmlspecialchars($data['redirect'] ?? '') ?>">
                    <?= $renderIcon('fingerprint', 'me-1') ?>Mit Passkey anmelden
                </button>
                <div class="text-danger small mt-2 d-none" id="passkey-login-error"></div>
            </div>
        </div>

        <div class="text-center mt-3 d-flex justify-content-center gap-3">
            <a href="/admin/forgot-password" class="text-secondary small text-decoration-none">Passwort vergessen?</a>
            <?php if (!empty($data['registrationEnabled'])): ?>
            <a href="/registrieren" class="text-secondary small text-decoration-none">Registrieren</a>
            <?php endif ?>
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
<script src="<?= $theme->assetUrl('js/esse-dashboard.js') ?>?v=20260608-inline-js-light-card"></script>
<script src="/public/assets/js/webauthn.js"></script>
<script src="<?= $theme->assetUrl('js/login.js') ?>?v=20260608-inline-js-light-card"></script>
</body>
</html>
