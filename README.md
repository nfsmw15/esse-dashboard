# esse-dashboard

Bootstrap-5-Dashboard-Theme für [esse-cms](https://github.com/nfsmw15/esse-cms) — Sidebar/App-Layout für geschützte Dashboard-, Mitglieder- und App-Bereiche.

[![Release](https://img.shields.io/github/v/release/nfsmw15/esse-dashboard?label=release&color=blue)](https://github.com/nfsmw15/esse-dashboard/releases)
[![License](https://img.shields.io/badge/license-AGPL--3.0--or--later-green)](LICENSE)
[![ESSE CMS](https://img.shields.io/badge/esse--cms-%3E%3D0.1.0-orange)](https://github.com/nfsmw15/esse-cms)

## Überblick

`esse-dashboard` ist für App-artige Bereiche gedacht — Mitgliederbereiche, Backends,
interne Tools — bei denen Inhalte hinter einer festen Navigation liegen, statt für
öffentliche Marketing-/Blog-Seiten:

- fixe Sidebar links mit Site-Name, Hauptmenü und User-Dropdown
- fixe Topbar mit Mobile-Menübutton und Light/Dark-Umschalter
- scrollender Contentbereich mit Seitenüberschrift im Content
- fester Footer am unteren Rand des Layouts
- eigenständiges Card-Layout (ohne Sidebar/Chrome) für `guest_only`-Seiten wie Registrierung
- theme-eigenes Rendering von Login- und Passwort-Seiten im Dashboard-Look
- Light- und Dark-Mode über Bootstrap `data-bs-theme`
- ESSE-UI-Komponenten-Support für Plugin-Ausgaben

## Voraussetzungen

- ESSE CMS >= 0.1.0
- PHP 8.x
- Bootstrap CSS: `/public/vendor/bootstrap/css/bootstrap.min.css`
- Bootstrap JS: `/public/vendor/bootstrap/js/bootstrap.bundle.min.js`
- Icon-Pack-CSS wird vom ESSE CMS Core geladen
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
    ├── LICENSE
    ├── assets/
    │   ├── css/
    │   │   └── esse-dashboard.css
    │   └── js/
    │       ├── esse-dashboard.js
    │       ├── login.js
    │       └── theme-init.js
    └── templates/
        ├── error.php
        ├── layout.php
        ├── standalone.php
        ├── login.php
        ├── forgot-password.php
        └── reset-password.php
```

Danach kann das Theme im ESSE Admin aktiviert werden.

## Manifest

`theme.json`:

```json
{
    "name": "esse-dashboard",
    "version": "0.0.9",
    "description": "Bootstrap 5 sidebar/app layout theme for dashboards and member areas.",
    "author": "ESSE CMS",
    "class": "EsseDashboard\\Theme",
    "menus": {
        "sidebar": "Sidebar-Navigation",
        "footer":  "Footer-Links"
    }
}
```

Pflichtfelder:

- `name` muss dem Theme-Verzeichnis entsprechen: `esse-dashboard`.
- `class` muss auf die Theme-Klasse zeigen: `EsseDashboard\Theme`.
- `menus` deklariert die Menü-Slots, die das Theme rendert (siehe nächster Abschnitt).

`theme.json` hat **kein** `requires`-Feld — der Core prüft beim Aktivieren keine
CMS-Mindestversion. Das ESSE-CMS-Badge oben ist rein informativ für Anwender.

Das GitHub-Repository muss für die CMS-Discovery das Topic `esse-theme` besitzen;
veröffentlichte Versionen werden über GitHub Releases gefunden.

## Menüs

| Slot | Settings-Key | Fallback-Slug | Zweck |
|---|---|---|---|
| `sidebar` | `theme_esse-dashboard_menu_sidebar` | `sidebar` | Hauptnavigation in der Sidebar |
| `footer` | `theme_esse-dashboard_menu_footer` | `footer` | Footer-Links |

Sidebar-Menüs unterstützen:

- normale Links
- Header-Einträge mit `type = header`
- Einträge mit Kindern als aufklappbare Untermenüs
- Icon-Klassen oder pack-agnostische Icon-Namen über das optionale `icon`-Feld

Wenn kein Icon gesetzt ist, rendert das Theme kein Fallback-Icon — dadurch entstehen
keine Platzhalter-Kreise vor Menüeinträgen. Pack-agnostische Icons werden über
`\Esse\Ui::icon()` gerendert; volle CSS-Klassen bleiben aus Kompatibilitätsgründen
möglich. Das Theme bindet kein festes Icon-Pack ein — die CSS-Datei des aktiven
Icon-Packs wird vom ESSE CMS Core geladen.

## Templates / Rendering-Logik

Die Template-Auswahl passiert zentral in `Theme.php`. Das Theme enthält **keine**
eigene Auth-/Sichtbarkeitslogik — `PageRenderer` prüft Zugriffsrechte bereits, bevor
`page.render` feuert.

| Zustand | Template | Verhalten |
|---|---|---|
| `error_code` gesetzt | `templates/error.php` | Vollbild-Fehlerseite |
| `visibility = guest_only` (Registrierung, Passwort-Reset, …) | `templates/standalone.php` | Card-Layout ohne Sidebar — minimale Topbar, zentrierter Inhalt, Footer |
| Alle übrigen erlaubten Seiten | `templates/layout.php` | Dashboard/App-Layout mit Sidebar, Topbar, Footer |

Zusätzlich rendert das Theme drei Auth-Seiten über eigene Hooks im Dashboard-Look
(Card-Layout, Light/Dark, Branding, Footer) statt im CMS-Admin-Design:

| Hook | Template | Seite |
|---|---|---|
| `auth.login.render` | `templates/login.php` | `/login` (Formular sendet Pflichtfeld `name="_form" value="admin_login"`) |
| `auth.forgot_password.render` | `templates/forgot-password.php` | `/admin/forgot-password` (inkl. Rechen-Captcha + Honeypot) |
| `auth.reset_password.render` | `templates/reset-password.php` | `/admin/reset-password` |

Die komplette Auth-/Mail-/Token-Logik (CSRF, Rate-Limiting, `Auth::attempt()`,
Captcha, Token-Erstellung/-Validierung) bleibt zentral im CMS-Core
(`admin/login.php`, `admin/forgot-password.php`, `admin/reset-password.php`) — das
Theme übernimmt ausschließlich das Rendering. `/admin/login` ignoriert den Hook
bewusst und bleibt als Fail-Safe-Notausgang immer beim Standard-Formular, falls das
Theme defekt ist oder deaktiviert wird. Für die Passwort-Seiten gibt es **keinen**
solchen Alias — schlägt das Rendering fehl, können Admins Passwörter weiterhin über
die Benutzerverwaltung zurücksetzen.

### Sidebar (`layout.php`)

- Site-Name + optionaler Slogan (Setting `site_slogan`, wird nur angezeigt, wenn nicht leer)
- Menü aus dem `sidebar`-Slot
- User-Dropdown unten mit `Profil`, `Admin` (ab Rolle `author`) und `Abmelden` (CSRF-geschützt)

### Topbar (`layout.php`)

Bewusst reduziert: Mobile-Burger-Button und Light/Dark-Umschalter. Der Seitentitel
steht im Contentbereich, nicht in der Topbar.

### Content (`layout.php`)

- `.dash-content` als scrollender Bereich
- `.dash-content-header` mit Seitentitel
- `.esse-content` als Container für gerenderten Seiteninhalt, füllt die volle
  verfügbare Breite (kein `max-width`-Cap)

Der Footer ist unterhalb des scrollenden Contentbereichs fix angeordnet
(`.dash-footer`) — Content scrollt zwischen Topbar und Footer, der Footer bleibt
sichtbar unten.

## Design-Tokens / CSS-Variablen

`assets/css/esse-dashboard.css` definiert eigene Tokens und mappt sie auf die
`--esse-*`-Variablen für ESSE-UI-Komponenten:

```css
:root {
    --sidebar-w: 260px;
    --topbar-h:  56px;
    --bg:        #ffffff;
    --sidebar-bg:#f8f9fa;
    --topbar-bg: #ffffff;
    --card-bg:   #ffffff;
    --border:    #dee2e6;
    --text:      #212529;
    --heading:   #212529;
    --muted:     #6c757d;
    --accent:    #0d6efd;
    --nav-hover: #e9ecef;
    --code-bg:   #f8f9fa;

    --esse-bg:         var(--bg);
    --esse-surface:    var(--card-bg);
    --esse-border:     var(--border);
    --esse-text:       var(--text);
    --esse-text-muted: var(--muted);
    --esse-radius:     .375rem;
    --esse-primary:    var(--accent);
    --esse-success:    #198754;
    --esse-warning:    #ffc107;
    --esse-danger:     #dc3545;
    --esse-info:       #0dcaf0;
}
```

Alle Basis-Tokens (`--bg`, `--card-bg`, `--accent`, …) werden unter
`[data-bs-theme="dark"]` mit eigenen Werten überschrieben.

Zusätzlich passt das Theme zentrale ESSE-UI- und Bootstrap-Komponenten optisch an:

- `.esse-panel`, `.esse-empty-state`, `.esse-panel-header`, `.esse-section-title`
- `.esse-table` (inkl. `--striped`-Variante und Hover-Kontrast in Light/Dark)
- `.esse-tabs-btn`
- `.esse-btn--primary`, `.btn-primary`, `.btn-secondary` — Outline-Stil (transparenter
  Hintergrund, Akzent-/Rahmenfarbe, Text in Akzentfarbe) für ein einheitliches
  Erscheinungsbild mit ESSE-UI-Buttons

## Light/Dark Mode

Das Theme unterstützt nur zwei Modi — `light` und `dark`, **kein** Auto-Modus, damit
Betriebssystem-/Browser-Einstellungen nicht ungewollt auf Dark zurückschalten.

Die frühe Initialisierung liegt in `assets/js/theme-init.js` und wird im `<head>`
geladen, damit die gespeicherte Auswahl vor dem ersten Rendern auf
`data-bs-theme` gesetzt wird. Die Dropdown-Interaktion und die aktive Icon-Anzeige
liegen in `assets/js/esse-dashboard.js`.

Die Auswahl wird in `localStorage` unter `esse-dashboard-theme` gespeichert:

```js
localStorage.setItem('esse-dashboard-theme', 'light');
localStorage.setItem('esse-dashboard-theme', 'dark');
```

Ungültige oder alte Werte wie `auto` fallen auf `light` zurück.

## esse-grid Support

Das Theme implementiert die für Plugin-Kompatibilität verpflichtenden Grid-Klassen:

- `.esse-grid-wrap`
- `.esse-grid`
- `.esse-grid[data-cols="2"]`
- `.esse-grid[data-cols="3"]`
- `.esse-grid[data-cols="4"]`
- `.esse-grid[data-cols="6"]`
- `.esse-grid-item`

Auf kleineren Viewports werden 3-, 4- und 6-Spalten-Grids auf zwei Spalten reduziert.

## ESSE-UI-Integration

Alle Templates laden Icon-Pack-CSS und ESSE UI nach Bootstrap und vor der Theme-CSS:

```html
<link rel="stylesheet" href="/public/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= htmlspecialchars($iconPackCss) ?>">
<link rel="stylesheet" href="/public/vendor/esse-ui/esse-ui.css">
<link rel="stylesheet" href="<?= $theme->assetUrl('css/esse-dashboard.css') ?>?v=...">
```

Theme-eigene JavaScript-Logik wird ausschließlich über Dateien aus `assets/js/`
eingebunden. Templates enthalten keine Inline-Skripte und keine Inline-Eventhandler.
Seitenspezifische Auth-Interaktion wie der Passkey-Button auf `/login` liegt in
`assets/js/login.js`; dynamische Daten werden als `data-*`-Attribute übergeben.

Page- und Theme-Icons werden pack-agnostisch über `Theme::renderIcon()` /
`\Esse\Ui::icon()` gerendert; volle CSS-Klassen (z. B. `bi bi-house`) bleiben als
Fallback kompatibel.

## Entwicklung / Deployment

PHP-Syntax aller Templates prüfen:

```bash
php -l Theme.php
for f in templates/*.php; do php -l "$f"; done
```

Arbeitsbaum prüfen:

```bash
git status --short
git diff --stat
```

Für manuelles Deployment müssen mindestens diese Dateien übertragen werden, wenn
Layout/CSS geändert wurden:

```text
assets/css/esse-dashboard.css
assets/js/*.js
templates/*.php
Theme.php
```

Der produktive Pfad hängt von der jeweiligen ESSE-CMS-Installation ab
(`themes/esse-dashboard/` im CMS-Root).

## Changelog

Siehe [CHANGELOG.md](CHANGELOG.md).

## Lizenz

[AGPL-3.0-or-later](LICENSE) — wie ESSE CMS Core.
