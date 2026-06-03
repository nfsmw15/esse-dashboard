# esse-dashboard

**Version:** 0.0.1  
Bootstrap 5 Sidebar/App-Layout-Theme für Dashboards und Member-Bereiche im ESSE CMS.

---

## Übersicht

`esse-dashboard` liefert ein klassisches App-Layout mit fixer Sidebar, Topbar und scrollbarem Hauptbereich. Das Theme unterscheidet automatisch zwischen angemeldeten Nutzern, öffentlichen Seiten und Fehlerseiten.

### Layouts

| Situation | Template | Beschreibung |
|---|---|---|
| Nutzer eingeloggt | `layout.php` | Vollständiges App-Layout mit Sidebar & Topbar |
| Seite `visibility = public`, nicht eingeloggt | `public.php` | Minimales Layout ohne Sidebar, mit Anmelden-Button |
| Nicht eingeloggt (sonstige Seiten) | `login.php` | Zentrierte Login-Karte |
| Fehler (`error_code` gesetzt) | `error.php` | Vollbild-Fehleranzeige (404, 500 …) |

---

## Voraussetzungen

- ESSE CMS (PHP 8.x)
- Bootstrap 5 unter `/public/vendor/bootstrap/`
- Bootstrap Icons unter `/public/vendor/bootstrap-icons/`

---

## Installation

Das Theme-Verzeichnis in den `themes/`-Ordner des ESSE CMS legen:

```
themes/
└── esse-dashboard/
    ├── theme.json
    ├── Theme.php
    ├── assets/
    │   └── css/
    │       └── esse-dashboard.css
    └── templates/
        ├── layout.php
        ├── login.php
        ├── public.php
        └── error.php
```

Anschließend das Theme im Admin-Bereich aktivieren.

---

## Konfiguration

### theme.json

```json
{
    "name": "esse-dashboard",
    "version": "0.0.1",
    "description": "Bootstrap 5 sidebar/app layout theme for dashboards and member areas.",
    "author": "ESSE CMS",
    "class": "EsseDashboard\\Theme",
    "menus": {
        "sidebar": "Sidebar-Navigation",
        "footer":  "Footer-Links"
    }
}
```

### Menüs

| Slot | Settings-Key | Standard-Slug | Beschreibung |
|---|---|---|---|
| `sidebar` | `theme_esse-dashboard_menu_sidebar` | `sidebar` | Hauptnavigation in der Sidebar |
| `footer` | `theme_esse-dashboard_menu_footer` | `footer` | Links im Footer (Login- & Public-Layout) |

Die Slugs werden über die ESSE-Einstellungen gespeichert (`settings`-Tabelle).

### Settings-Keys (Datenbank)

| Key | Beschreibung |
|---|---|
| `site_name` | Wird im Brand-Bereich der Sidebar und im `<title>` angezeigt |
| `theme_esse-dashboard_menu_sidebar` | Slug des Sidebar-Menüs |
| `theme_esse-dashboard_menu_footer` | Slug des Footer-Menüs |

---

## Seitentypen & Sichtbarkeit

Die Anzeige hängt vom `visibility`-Feld der Seite und vom Login-Status ab:

```
Seite aufgerufen
│
├─ error_code gesetzt → error.php
│
├─ Nutzer eingeloggt → layout.php
│
└─ Nicht eingeloggt
   ├─ visibility = "public" → public.php
   └─ sonst → login.php
```

**Tipp:** Seiten, die ohne Login erreichbar sein sollen (z. B. Impressum, AGB), einfach auf `visibility = public` setzen.

---

## Sidebar-Navigation

### Menütypen

| Typ | Verhalten |
|---|---|
| Normaler Link | Wird als `<a>` gerendert, mit `active`-Klasse wenn der Slug übereinstimmt |
| `header` | Wird als Beschriftungszeile (`nav-section`) gerendert, nicht klickbar |
| Link mit Kindern | Aufklappbares Untermenü (`dash-has-children`) |

### Icons

Jedes Menüelement und jede Seite kann ein `icon`-Feld tragen. Es wird als Bootstrap-Icons-Klasse gerendert:

```html
<i class="bi bi-speedometer2"></i>
```

Im Sidebar-Link:
```php
<?php if (!empty($item['icon'])): ?>
<i class="<?= htmlspecialchars($item['icon']) ?>"></i>
<?php endif ?>
```

In der Topbar-Überschrift:
```php
<?php if (!empty($page['icon'])): ?>
<i class="<?= htmlspecialchars($page['icon']) ?> me-2"></i>
<?php endif ?>
```

### Untermenüs

Menüelemente mit Kindeinträgen werden aufklappbar dargestellt. Das Untermenü öffnet sich automatisch, wenn eines der Kinder aktiv ist. Ein Klick auf einen Eintrag mit echter URL navigiert direkt; nur Einträge mit `href="#"` toggeln das Untermenü.

---

## CSS & Design Tokens

Alle Farben und Abstände sind über CSS-Variablen steuerbar:

```css
:root {
    --sidebar-w:  260px;   /* Breite der Sidebar */
    --topbar-h:   56px;    /* Höhe der Topbar */
    --bg:         #0f0f0f; /* Seitenhintergrund */
    --sidebar-bg: #111318; /* Sidebar- & Topbar-Hintergrund */
    --card-bg:    #1a1d23; /* Karten-Hintergrund */
    --border:     #2a2d35; /* Trennlinien */
    --text:       #dee2e6; /* Fließtext */
    --muted:      #6c757d; /* Gedämpfter Text */
    --accent:     #6ea8fe; /* Akzentfarbe (aktiver Sidebar-Link) */
}
```

### Hilfsklassen

| Klasse | Beschreibung |
|---|---|
| `.dash-card` | Einheitliche Karte mit `--card-bg`, Border und abgerundeten Ecken |
| `.esse-content` | Prosa-Container: Typografie für `h1–h3`, Links, `pre`, `code` |

---

## Responsive Verhalten

Unterhalb von 992 px (Bootstrap `lg`) wird die Sidebar ausgeblendet und über einen Burger-Button in der Topbar eingeblendet. Ein transparenter Overlay schließt die Sidebar beim Klick daneben.

```js
function toggleSidebar() {
    document.getElementById('dash-sidebar').classList.toggle('open');
    document.getElementById('sidebar-overlay').classList.toggle('show');
}
```

---

## Sidebar-Footer

Der Sidebar-Footer zeigt für eingeloggte Nutzer den Display-Namen, einen Profil-Link (`/profil`) und einen Abmelden-Button (CSRF-gesichert). Für nicht eingeloggte Nutzer wird ein Anmelden-Link angezeigt.

---

## Admin-Link in der Topbar

Nutzer mit der Rolle `author` oder höher sehen in der Topbar einen „Admin"-Button, der direkt zu `/admin` führt.

---

## Changelog

### 0.0.1 — 2026-06-03
- Initiale Version
- Bootstrap 5 Sidebar/App-Layout
- Login-, Public- und Error-Templates
- Responsive Mobile-Sidebar mit Overlay
- Aufklappbare Untermenüs
- Icon-Unterstützung für Seiten und Menüeinträge
- Öffentliche Seiten via `visibility = public` ohne Login erreichbar
