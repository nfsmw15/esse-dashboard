<?php

declare(strict_types=1);

namespace EsseDashboard;

use Esse\DB;
use Esse\Hooks;
use Esse\Menu;

class Theme extends \Esse\Theme
{
    private array $settings = [];

    public function boot(): void
    {
        $ts = DB::table('settings');
        $rows = DB::fetchAll("SELECT `key`, `value` FROM `{$ts}`");
        $this->settings = array_column($rows, 'value', 'key');

        Hooks::on('page.render', [$this, 'renderPage']);
        Hooks::on('auth.login.render', [$this, 'renderLogin']);
    }

    public function renderPage(array $page, string $content): void
    {
        $siteName    = $this->settings['site_name']   ?? 'ESSE CMS';
        $siteSlogan  = $this->settings['site_slogan'] ?? '';
        $sidebarSlug = $this->settings['theme_esse-dashboard_menu_sidebar'] ?? 'sidebar';
        $footSlug    = $this->settings['theme_esse-dashboard_menu_footer']  ?? 'footer';
        $footMenu    = $footSlug ? Menu::get($footSlug) : [];
        $iconPackCss = $this->activeIconPackCssUrl();
        $theme       = $this;

        if (!empty($page['error_code'])) {
            require $this->basePath('templates/error.php');
            return;
        }

        // Guest-only pages (login, register, password reset, …) get a
        // standalone layout without the dashboard sidebar/chrome.
        if (($page['visibility'] ?? '') === 'guest_only') {
            require $this->basePath('templates/standalone.php');
            return;
        }

        $sidebarMenu = Menu::get($sidebarSlug);
        require $this->basePath('templates/layout.php');
    }

    public function renderLogin(array $data): void
    {
        $iconPackCss = $this->activeIconPackCssUrl();
        $theme       = $this;
        require $this->basePath('templates/login.php');
    }

    public function renderIcon(?string $icon, string $class = ''): string
    {
        if (empty($icon)) {
            return '';
        }

        if (str_contains($icon, ' ')) {
            return '<i class="' . htmlspecialchars(trim($icon . ' ' . $class)) . '"></i>';
        }

        $iconHtml = \Esse\Ui::icon(preg_replace('/^(bi|ph|ti|lucide|ri)-/', '', $icon));
        return $class === '' ? $iconHtml : '<span class="' . htmlspecialchars($class) . '">' . $iconHtml . '</span>';
    }

    private function activeIconPackCssUrl(): string
    {
        $packName = $this->settings['icon_pack'] ?? 'bootstrap-icons';
        $packDir  = preg_replace('/[^a-z0-9\-]/', '', $packName) ?: 'bootstrap-icons';
        $packJson = ESSE_ROOT . '/public/vendor/' . $packDir . '/iconpack.json';

        if (!is_file($packJson)) {
            $packDir  = 'bootstrap-icons';
            $packJson = ESSE_ROOT . '/public/vendor/bootstrap-icons/iconpack.json';
        }

        $cssFile = 'bootstrap-icons.min.css';
        if (is_file($packJson)) {
            $meta = json_decode((string) file_get_contents($packJson), true);
            if (is_array($meta) && !empty($meta['css'])) {
                $cssFile = basename((string) $meta['css']);
            }
        }

        return '/public/vendor/' . $packDir . '/' . $cssFile;
    }
}
