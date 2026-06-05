# Changelog

## 0.0.5 - 2026-06-06

- Fixed `.esse-btn--primary`: changed from filled to outline style (transparent background, accent border and text color) for consistent appearance across light and dark mode.
- Fixed `.esse-table` hover: corrected selector from `.table` (Bootstrap class) to `.esse-table` so row hover actually fires on plugin tables.
- Fixed `.esse-table--striped` alternating row background.
- Fixed Light-Mode `.esse-table` hover visibility: `#d8dde4` instead of `#e9ecef` for sufficient contrast on white surface.
- Removed `max-width: 1200px` cap from content area so it fills the available width without horizontal scrolling.

## 0.0.4 - 2026-06-05

- Added active icon-pack stylesheet loading to theme templates so `\Esse\Ui::icon()` output is visible in frontend layouts.

## 0.0.3 - 2026-06-05

- Removed theme-side Bootstrap Icons dependency and rely on the CMS-managed active icon pack.
- Rendered page and theme icons through `\Esse\Ui::icon()` while keeping full CSS classes compatible.
- Hardened menu template access for missing `type`, `target`, and `label` fields.
- Replaced hard-coded error-page heading color with a theme token.
- Updated README to document pack-agnostic icon handling.

## 0.0.2 - 2026-06-04

- Added ESSE UI stylesheet loading to all templates.
- Added ESSE UI design tokens and component overrides.
- Added required ESSE grid classes for plugin compatibility.
- Added Light/Dark theme switcher and removed Auto mode.
- Moved page title from topbar into the content header.
- Kept site name in the sidebar brand area.
- Reworked sidebar footer into a user dropdown with profile, admin and logout actions.
- Removed fallback circle icons from menu entries without configured icons.
- Removed search and admin action from the topbar.
- Made the main content area scroll independently between fixed topbar and footer.
- Kept footer visible at the bottom of the dashboard layout.
- Moved the content scrollbar to the right edge of the main area while keeping content width constrained.

## 0.0.1 - 2026-06-03

- Initial dashboard theme.
- Added Bootstrap 5 sidebar/app layout.
- Added login, public and error templates.
- Added responsive mobile sidebar with overlay.
- Added configurable sidebar and footer menus.
