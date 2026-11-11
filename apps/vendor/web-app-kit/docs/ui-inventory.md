# UI-Inventar der Web-Tools (Stand 2026-10-07)

> Hinweis: Die Tabellen je App geben den Stand vor der Migration auf Kit 0.3.0 wieder
> (lokale `components/ui`-Kopien, lucide, eigene Theme-Toggles). Sie werden nicht laufend
> nachgeführt.

Erhoben read-only aus `~/Development/{layerboard,mockup,workbench,splitter,taste}/frontend`.
Vergleich der gleichnamigen `components/ui/*`-Dateien nach Normalisierung mit Prettier
(`--print-width 100 --no-semi --trailing-comma all`), damit reine Formatierungs-Unterschiede
(Semikolons, Import-Reihenfolge, Zeilenumbruch) nicht als Abweichung zählen.

## Setup je App

| App | shadcn style | baseColor | cssVariables | iconLibrary | Tailwind | Theme-CSS |
| --- | --- | --- | --- | --- | --- | --- |
| layerboard | radix-nova | neutral | true | lucide | 4.3.2 (`@tailwindcss/vite`) | Kit `theme.css` + Chart-Tokens, Geist |
| mockup | radix-nova | neutral | true | lucide (+ `@tabler/icons-react` ^3.46.0) | 4.3.2 | Kit `theme.css` + Canvas-Tokens |
| workbench | kein `components.json` | – | – | lucide | 4.3.2 | Kit `theme.css` |
| splitter | radix-nova | neutral | true | tabler (3.49.0) | 4.3.2 | eigene Tokens (orange Akzent, Series/Status), Noto Sans; kein Kit `theme.css` |
| taste | radix-nova | neutral | true | lucide | 4.3.2 | Kit `theme.css`, System-Font |

Gemeinsam in allen fünf: `react`/`react-dom` 19.2.7, `radix-ui` 1.6.2, `class-variance-authority`
0.7.1, `clsx` 2.1.1, `tailwind-merge` 3.6.0, `tw-animate-css` 1.4.0, `shadcn` 4.13.0 (für
`shadcn/tailwind.css`), `@types/react` 19.2.17. CSS-Kopf überall:
`@import "tailwindcss"; @import "tw-animate-css"; @import "shadcn/tailwind.css";`.
Einbindung des Kits auf `main` aller Apps als `file:../../web-app-kit/packages/frontend`
(Geschwister-Checkout, in Docker als `../web-app-kit:/web-app-kit:ro` gemountet). Ziel-Layout
ist das Git-Submodule `<app>/vendor/web-app-kit` mit `file:../vendor/web-app-kit/packages/...`
und `install-links=true` in `frontend/.npmrc`; mockup und layerboard nutzen es bereits auf
`feat/production-setup`.

`lib/utils.ts` (`cn`) ist in allen Apps inhaltlich identisch (nur Semikolons unterschiedlich).

## Komponenten-Vergleich

| Komponente | Apps | Befund | Im Kit |
| --- | --- | --- | --- |
| button | layerboard, mockup, workbench, splitter, taste | layerboard = taste identisch; workbench identisch bis auf fehlenden `buttonVariants`-Export; splitter +`active:aria-[haspopup]:opacity-80`; mockup abweichend (`disabled:cursor-not-allowed`, `aria-pressed`-States, `border-black` bei default) | ja (Basis layerboard/taste; Optionen aus mockup/splitter übernommen: `aria-pressed`, `active:aria-[haspopup]:opacity-80`, `disabled:cursor-not-allowed`; `border-black` entfällt) |
| input | layerboard, mockup, splitter, taste | identisch | ja |
| label | layerboard, mockup, splitter, taste | identisch | ja |
| textarea | layerboard, mockup, splitter | identisch | ja |
| badge | layerboard, splitter, taste | identisch | ja |
| tooltip | layerboard, workbench, splitter | identisch (splitter nur `"use client"`) | ja |
| card | layerboard, mockup, splitter | layerboard = splitter identisch; mockup Teilmenge (ohne `CardAction`) | ja |
| table | layerboard, splitter | identisch | ja |
| dropdown-menu | layerboard, splitter | identisch bis auf Icons (lucide vs. Tabler) | ja (Tabler) |
| dialog | layerboard, mockup, splitter, taste | layerboard = splitter bis auf Icons; taste/mockup Teilmengen mit i18n-`sr-only`-Label via `useLanguage()`; Upstream hat hart codiertes englisches `Close` | ja, mit Pflicht-Prop `closeLabel` |
| select | layerboard, mockup, splitter, taste | layerboard = splitter bis auf Icons; taste/mockup ältere, gekürzte Fassung (kein `size`, keine Scroll-Buttons, taste-Default `position="popper"`, mockup ohne Animation) | ja (Basis layerboard/splitter: `item-aligned`, `IconSelector`) |
| alert-dialog, chart, progress, separator, skeleton, sonner, switch, tabs | nur splitter | app-spezifisch | nein |
| checkbox | nur taste | app-spezifisch | nein |
| context-menu | nur mockup | app-spezifisch | nein |
| popover | nur mockup | Einstellungs-Popups in Werkzeugleisten | ja (ab 0.10.0, Stil wie `DropdownMenuContent`) |
| toggle, toggle-group | keine | Werkzeugleisten mit klar erkennbarem aktivem Werkzeug (erstes Ziel: Layout-Editor in mockup) | ja (ab 0.10.0, Aktiv-Zustand `--primary`/`--primary-foreground`) |

## Theme-Toggle / Theme-Provider je App

| App | Theme-Provider | Toggle |
| --- | --- | --- |
| layerboard | Re-Export Kit | kein Header-Toggle; 3 Buttons im SettingsPanel |
| mockup | eigene Kopie (nicht Kit) | Zyklus Light → Dark → System, lucide, `outline`/`sm`, i18n-Title |
| workbench | Re-Export Kit | Zyklus Light → Dark → System, lucide, `ghost`/`icon-sm`, i18n-Title |
| splitter | Re-Export Kit | Dropdown mit Radio-Items, Tabler, hart codiert deutsch |
| taste | Re-Export Kit | Zyklus Light → Dark → System, lucide, `outline`/`sm`, i18n-Title |

Dateien mit `lucide-react`-Import (inkl. `components/ui`): layerboard 23, mockup 19, taste 11,
workbench 8, splitter 0.

## Shell / Layout (ab 0.4.0)

Gemeinsamer Rahmen aller Web-Tools
(Kopfzeile `h-14`, `border-b`, `px-4`, Skip-Link, Inhaltsbereich mit `id` und `tabIndex={-1}`).

| Baustein | Im Kit | Zweck |
| --- | --- | --- |
| `AppShell` | ja | Kopfzeile (Marke, App-Aktionen, Sprache/Theme) + optional Navigation links + optional Detail rechts; Landmarks `header`/`nav`/`main`/`aside` |
| `SidePanel` | ja | Panel-Fläche für beide Seiten, `--sidebar-*`-Tokens, Breiten 16/20/24rem, optionaler Kopf, `open` gesteuert |
| `PageContainer` | ja | zentrierte Seitenbreite `md`/`lg`/`full` |
| `LanguageToggle` | ja | Sprachwechsel im Stil des `ThemeToggle` |
| `themeInitScript` | ja (Paket-Root) | setzt die Theme-Klasse vor dem React-Mount |
| Noto Sans | `--font-sans` in `theme.css`, Dateien über `@fontsource-variable/noto-sans` | lokale Schrift, System-Fallback |
