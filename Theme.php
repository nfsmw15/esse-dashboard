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
    }

    public function renderPage(array $page, string $content): void
    {
        $siteName    = $this->settings['site_name'] ?? 'ESSE CMS';
        $sidebarSlug = $this->settings['theme_esse-dashboard_menu_sidebar'] ?? 'sidebar';
        $footSlug    = $this->settings['theme_esse-dashboard_menu_footer']  ?? 'footer';
        $footMenu    = $footSlug ? Menu::get($footSlug) : [];
        $iconPackCss = $this->activeIconPackCssUrl();
        $theme       = $this;

        if (!empty($page['error_code'])) {
            require $this->basePath('templates/error.php');
            return;
        }

        // Not logged in
        if (!\Esse\Auth::check()) {
            // Respect the page's visibility setting:
            // 'public' → show content (uses minimal layout without sidebar)
            // 'members', 'admin' or anything else → show login
            if (($page['visibility'] ?? '') === 'public' && empty($page['error_code'])) {
                require $this->basePath('templates/public.php');
                return;
            }
            require $this->basePath('templates/login.php');
            return;
        }

        $sidebarMenu = Menu::get($sidebarSlug);
        require $this->basePath('templates/layout.php');
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
