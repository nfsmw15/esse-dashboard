<?php
/**
 * Theme-rendered /admin/forgot-password page (auth.forgot_password.render hook).
 * No sidebar — minimal topbar, centered card, footer.
 *
 * Auth/mail logic (CSRF, rate-limiting, captcha, token creation) stays in
 * admin/forgot-password.php — this template only renders the form.
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
    <title><?= htmlspecialchars('Passwort vergessen — ' . $brandName) ?></title>
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
            <small class="text-secondary">Passwort zurücksetzen</small>
        </div>

        <?php if (!empty($data['sent'])): ?>
        <div class="alert alert-success">
            Falls ein Account mit dieser E-Mail-Adresse existiert, wurde ein Reset-Link versendet.
            Bitte prüfe deinen Posteingang.
        </div>
        <a href="/login" class="btn btn-outline-secondary w-100">Zurück zum Login</a>

        <?php else: ?>

        <?php if (!empty($data['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($data['errors'] as $error): ?><div><?= htmlspecialchars($error) ?></div><?php endforeach ?>
        </div>
        <?php endif ?>

        <div class="dash-card esse-content esse-content--standalone">
            <p class="text-secondary small mb-3">
                Gib deine E-Mail-Adresse ein. Du erhältst einen Link zum Zurücksetzen deines Passworts.
            </p>
            <form method="post">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($data['csrfToken'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">E-Mail</label>
                    <input type="email" name="email" class="form-control" autocomplete="username" autofocus required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= htmlspecialchars($data['captchaQuestion'] ?? '') ?> = ?</label>
                    <input type="text" name="captcha_answer" class="form-control" inputmode="numeric"
                           autocomplete="off" required>
                </div>
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <label for="fp-website">Website</label>
                    <input type="text" id="fp-website" name="<?= htmlspecialchars($data['honeypotField'] ?? '') ?>"
                           tabindex="-1" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary w-100">Link senden</button>
            </form>
        </div>
        <div class="text-center mt-3">
            <a href="/login" class="text-secondary small text-decoration-none">Zurück zum Login</a>
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
