# esse-dashboard

Bootstrap-5-Dashboard-Theme für ESSE CMS. Das Theme ist für geschützte Dashboard-, Mitglieder- und App-Bereiche gedacht und bringt Sidebar, Topbar, Login-, Public- und Error-Layouts mit.

**Aktuelle Manifest-Version:** `0.0.2`

## Überblick

`esse-dashboard` rendert angemeldete Nutzer in einem festen App-Layout:

- fixe Sidebar links mit Site-Name, Menü und User-Dropdown
- fixe Topbar mit Mobile-Menübutton und Light/Dark-Umschalter
- scrollender Contentbereich mit Seitenüberschrift im Content
- fester Footer am unteren Rand des Layouts
- Light- und Dark-Mode über Bootstrap `data-bs-theme`
- ESSE-UI-Komponenten-Support für Plugin-Ausgaben

Nicht angemeldete Nutzer sehen entweder ein Public-Layout oder den Login-Screen.

## Voraussetzungen

- ESSE CMS
- PHP 8.x
- Bootstrap CSS: `/public/vendor/bootstrap/css/bootstrap.min.css`
- Bootstrap JS: `/public/vendor/bootstrap/js/bootstrap.bundle.min.js`
- Bootstrap Icons: `/public/vendor/bootstrap-icons/bootstrap-icons.min.css`
- ESSE UI: `/public/vendor/esse-ui/esse-ui.css`

## Installation

Das Theme-Verzeichnis muss im ESSE CMS unter `themes/esse-dashboard` liegen:

```text
themes/
└── esse-dashboard/
    ├── theme.json
    ├── Theme.php
    ├── README.md
    ├── CHANGELOG.md
    ├── assets/
    │   └── css/
    │       └── esse-dashboard.css
    └── templates/
        ├── error.php
        ├── layout.php
        ├── login.php
        └── public.php
```

Danach kann das Theme im ESSE Admin aktiviert werden.

## Manifest

`theme.json`:

```json
{
    "name": "esse-dashboard",
    "version": "0.0.2",
    "description": "Bootstrap 5 sidebar/app layout theme for dashboards and member areas.",
    "author": "ESSE CMS",
    "class": "EsseDashboard\\Theme",
    "menus": {
        "sidebar": "Sidebar-Navigation",
        "footer": "Footer-Links"
    }
}
```

Wichtig für ESSE CMS:

- `name` muss dem Theme-Verzeichnis entsprechen: `esse-dashboard`.
- `class` muss auf die Theme-Klasse zeigen: `EsseDashboard\Theme`.
- Das GitHub-Repository muss für die CMS-Discovery das Topic `esse-theme` besitzen.
- Veröffentlichte Versionen werden über GitHub Releases gefunden.

## Rendering-Logik

Die Auswahl des Templates passiert in `Theme.php`.

| Zustand | Template | Verhalten |
|---|---|---|
| `error_code` gesetzt | `templates/error.php` | Vollbild-Fehlerseite |
| Nutzer ist angemeldet | `templates/layout.php` | Dashboard/App-Layout mit Sidebar |
| Nicht angemeldet und `visibility = public` | `templates/public.php` | Public-Layout ohne Sidebar |
| Nicht angemeldet und geschützt | `templates/login.php` | Login-Screen |

## Menüs

Das Theme deklariert zwei Menü-Slots.

| Slot | Settings-Key | Fallback-Slug | Zweck |
|---|---|---|---|
| `sidebar` | `theme_esse-dashboard_menu_sidebar` | `sidebar` | Hauptnavigation in der Sidebar |
| `footer` | `theme_esse-dashboard_menu_footer` | `footer` | Footer-Links |

Sidebar-Menüs unterstützen:

- normale Links
- Header-Einträge mit `type = header`
- Einträge mit Kindern als aufklappbare Untermenüs
- Bootstrap-Icon-Klassen über das optionale `icon` Feld

Wenn kein Icon gesetzt ist, rendert das Theme kein Fallback-Icon. Dadurch entstehen keine Platzhalter-Kreise vor Menüeinträgen.

## Layout

### Sidebar

Die Sidebar ist links fixiert und enthält:

- Site-Name mit kleinem Claim
- Menü aus dem `sidebar` Slot
- User-Dropdown unten

Das User-Dropdown enthält:

- `Profil`
- `Admin` für Nutzer ab Rolle `author`
- `Abmelden` mit CSRF-Token

### Topbar

Die Topbar ist fixiert und bewusst reduziert:

- Mobile-Burger-Button
- Light/Dark-Umschalter

Der Seitentitel steht nicht in der Topbar, sondern im Contentbereich.

### Content

Der Contentbereich besteht aus:

- `.dash-content` als scrollender Bereich
- `.dash-content-header` mit Seitentitel
- `.esse-content` als Container für gerenderten Seiteninhalt

Die Scrollleiste sitzt am rechten Rand des Main-Bereichs. Der eigentliche Inhalt ist auf `1200px` begrenzt.

### Footer

Der Footer ist unterhalb des scrollenden Contentbereichs fix im Dashboard-Layout angeordnet:

- Content scrollt zwischen Topbar und Footer
- Footer bleibt sichtbar unten
- Footer-Linie ist über `.dash-footer` definiert

## Light/Dark Mode

Das Theme unterstützt nur zwei Modi:

- `light`
- `dark`

Es gibt keinen Auto-Modus. Dadurch wird vermieden, dass Betriebssystem- oder Browser-Einstellungen ungewollt auf Dark zurückschalten.

Die Auswahl wird in `localStorage` unter `esse-dashboard-theme` gespeichert.

```js
localStorage.setItem('esse-dashboard-theme', 'light');
localStorage.setItem('esse-dashboard-theme', 'dark');
```

Ungültige oder alte Werte wie `auto` fallen auf `light` zurück.

## ESSE UI

Alle Templates laden ESSE UI nach Bootstrap und vor der Theme-CSS:

```html
<link rel="stylesheet" href="/public/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="/public/vendor/esse-ui/esse-ui.css">
<link rel="stylesheet" href="<?= $theme->assetUrl('css/esse-dashboard.css') ?>?v=...">
```

`assets/css/esse-dashboard.css` setzt die `--esse-*` Variablen:

```css
:root {
    --esse-bg: var(--bg);
    --esse-surface: var(--card-bg);
    --esse-border: var(--border);
    --esse-text: var(--text);
    --esse-text-muted: var(--muted);
    --esse-radius: .375rem;
    --esse-primary: var(--accent);
    --esse-success: #198754;
    --esse-warning: #ffc107;
    --esse-danger: #dc3545;
    --esse-info: #0dcaf0;
}
```

Zusätzlich werden zentrale ESSE-UI-Komponenten optisch an das Theme angepasst:

- `.esse-panel`
- `.esse-empty-state`
- `.esse-panel-header`
- `.esse-section-title`
- `.esse-table`
- `.esse-tabs-btn`
- `.esse-btn--primary`

## ESSE Grid

Das Theme implementiert die verpflichtenden Grid-Klassen für Plugins:

- `.esse-grid-wrap`
- `.esse-grid`
- `.esse-grid[data-cols="2"]`
- `.esse-grid[data-cols="3"]`
- `.esse-grid[data-cols="4"]`
- `.esse-grid[data-cols="6"]`
- `.esse-grid-item`

Auf kleineren Viewports werden 3-, 4- und 6-Spalten-Grids auf zwei Spalten reduziert.

## CSS-Tokens

Wichtige Theme-Variablen:

```css
:root {
    --sidebar-w: 260px;
    --topbar-h: 56px;
    --bg: #ffffff;
    --sidebar-bg: #f8f9fa;
    --topbar-bg: #ffffff;
    --card-bg: #ffffff;
    --border: #dee2e6;
    --text: #212529;
    --heading: #212529;
    --muted: #6c757d;
    --accent: #0d6efd;
    --nav-hover: #e9ecef;
    --code-bg: #f8f9fa;
}
```

Für Dark Mode werden diese Werte unter `[data-bs-theme="dark"]` überschrieben.

## Responsive Verhalten

Unterhalb von `991px`:

- Sidebar ist standardmäßig ausgeblendet
- Burger-Button in der Topbar öffnet die Sidebar
- Overlay schließt die Sidebar bei Klick außerhalb
- Main-Bereich nutzt die volle Breite

## Entwicklung

PHP-Syntax prüfen:

```bash
php -l Theme.php
php -l templates/layout.php
php -l templates/login.php
php -l templates/public.php
php -l templates/error.php
```

Arbeitsbaum prüfen:

```bash
git status --short
git diff --stat
```

## Deployment

Für manuelles Deployment müssen mindestens diese Dateien übertragen werden, wenn Layout/CSS geändert wurden:

```text
assets/css/esse-dashboard.css
templates/layout.php
templates/login.php
templates/public.php
templates/error.php
```

Der produktive Pfad kann je nach Installation abweichen. In der aktuellen Umgebung wurde per SFTP nach folgendem Theme-Verzeichnis deployt:

```text
/home/petereita/web/esse.nfsmw15.de/public_html/themes/esse-dashboard
```

## Changelog

Siehe `CHANGELOG.md`.
