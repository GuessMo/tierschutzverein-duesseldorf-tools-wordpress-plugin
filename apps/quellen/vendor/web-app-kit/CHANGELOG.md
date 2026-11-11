# web-app-kit — Changelog

Semantische Versionierung, eine Kit-Version für alle Pakete (`VERSION` = Versionen in
`packages/*/package.json`). Jede Fassung ist ein Git-Tag `vX.Y.Z` auf `origin/main`.
Apps binden den Kit als Git-Submodule `vendor/web-app-kit`; der gepinnte Tag ist die
Version, gegen die die App gebaut ist.

- **PATCH** = Bugfix ohne Änderung an der App. App-Aktion: keine.
- **MINOR** = neue oder geänderte Funktion/Komponente, abwärtskompatibel. App-Aktion: optional.
- **MAJOR** = Breaking Change. App-Aktion: Pflicht, mit Umbau-Liste im Eintrag.
- Änderungen ohne Paket-Code (Tooling, Scripts, Doku) = **PATCH**, App-Aktion: keine.

Jeder Eintrag hat eine Zeile `App-Aktion: keine | optional | Pflicht …`.
Einträge bis 0.3.1 sind nachträglich aus der Git-Historie abgeleitet (dort leichtgewichtige
Tags, Datum = Commit-Datum); deren Versionsnummern folgen der Regel oben noch nicht.

## [0.9.0] — 2026-11-04

- **Neu:** Primärfarbe anpassbar: `--primary`, `--primary-foreground`, `--ring` und die
  `--sidebar-*`-Entsprechungen stehen in `packages/frontend/src/brand.css` (Hell und Dunkel),
  ein Test prüft sie gegen WCAG 2.2 AA (README „Primärfarbe").
- **Neu:** `contrastRatio()` und `relativeLuminance()` (OKLCH und `#rrggbb`) für eigene
  Abweichungen per `applyThemeOverrides`.
- **Neu:** Agent-Skills unter `.agents/skills/` (`web-app-kit-new-tool`, `web-app-kit-ui`,
  `web-app-kit-update`) und `scripts/link-agent-skills.sh`, das sie in einem Tool verlinkt
  (README „Agent-Skills").
- **Geändert:** `.gitignore` ignoriert `.mcp.json.pre-migration-*`.

App-Aktion: optional. Nach dem Pin-Bump `bash vendor/web-app-kit/scripts/link-agent-skills.sh`
ausführen und im `AGENTS.md` des Tools auf die Skills verweisen.

## [0.8.0] — 2026-11-04

- **Neu:** `AppShell` hat einen optionalen `footer`-Slot (Landmark `contentinfo`).
- **Neu:** `AppVersion` zeigt den ausgelieferten Stand (`git describe --tags`) mit Beschriftung
  aus der App.
- **Neu:** Backend `readAppVersion()` (liest `APP_VERSION`) und `registerVersionRoute()`
  (`GET /api/version`, `no-store`).

App-Aktion: optional. Für die Versionsanzeige: Pipeline mit voller Tag-Historie,
`--build-arg APP_VERSION`, im Dockerfile `ARG`/`ENV` (`VITE_APP_VERSION`, `APP_VERSION`),
`footer={<AppVersion …/>}` in der Shell, `registerVersionRoute` im Server (README
„Version im Footer").

## [0.7.1] — 2026-11-03

- **Behoben:** Paket-Tests von backend und frontend laufen mit
  `NODE_OPTIONS=--no-experimental-webstorage`, damit der eingebaute `localStorage` von Node
  die DOM-Tests nicht stört.

App-Aktion: keine

## [0.7.0] — 2026-10-14

- **Geändert:** `describeError` zeigte bei jedem `Error` die rohe Meldung, auch Browser-
  Netzwerkfehler (`TypeError: Failed to fetch`) und JSON-Parse-Fehler bei Nicht-JSON-Antworten.
  Jetzt nur noch die Meldung eines `ApiError` oder eines `UserFacingError`, sonst den übergebenen
  Fallback.
- **Neu:** `UserFacingError` für Meldungen, die die App selbst für Nutzer wirft.

App-Aktion: optional; Pflicht vor dem Pin-Bump, wenn die App eigene `Error`-Meldungen über
`describeError` anzeigt: diese als `UserFacingError` werfen (sonst erscheint der Fallback).

## [0.6.1] — 2026-10-13

- **Doku:** README „ESLint-Plugin" nennt die `COPY`-Zeile für `packages/eslint-plugin` in
  `frontend/Dockerfile.dev` und `Dockerfile` der App. Ohne sie bricht `npm ci` im Image mit
  `ENOENT` ab (lokaler Stack und Pipeline-Step für das Image).

App-Aktion: keine (Dockerfiles prüfen, falls das Plugin schon eingebunden ist)

## [0.6.0] — 2026-10-13

- **Neu:** Paket `@web-app-kit/eslint-plugin` mit Regel `interactive-states` und Config
  `configs.recommended`. Die Regel meldet interaktive DOM-Elemente (`button`, `a` mit `href`
  oder `onClick`, `onClick`/`onKeyDown`, interaktive Rollen) ohne `hover:`, `active:` und
  `focus-visible:` im `className`, sofern keine Kit-Klassen genutzt werden, sowie
  unsichtbaren Hover (`hover:bg-muted`, `hover:bg-accent`). Dazu vier `jsx-a11y`-Regeln.
  Parser: `@babel/eslint-parser`, funktioniert mit TypeScript 7. Voraussetzung: Node
  `^22.18.0 || >=24.11.0` (Babel 8).
- **Behoben:** `Badge` als Link hat jetzt einen Pressed-Zustand (vorher nur Hover und Fokus).
- **Geändert:** `scripts/tag-release.sh` prüft die Version aller `packages/*/package.json`
  (vorher feste Liste mit backend und frontend) und bricht ab, wenn kein Paket gefunden wird.

App-Aktion: optional für den Pin; die Einbindung des Plugins ist für Kit-Apps laut `AGENTS.md`
verpflichtend (Ablauf README „ESLint-Plugin", Altfälle per `--suppress-all` erfassen)

## [0.5.0] — 2026-10-12

- **Geändert:** `AppShell` — Navigation und Detailbereich sind unterhalb von `md` einklappbar.
  Eine Leiste mit `navigationLabel` bzw. `detailLabel` und Chevron schaltet den Bereich
  (`aria-expanded`, `aria-controls`, Fokus-Ring nach innen). Start eingeklappt, offen höchstens
  60 dvh (vorher fest 30 dvh). Ab `md` unverändert.

App-Aktion: optional (Pin anheben, Kit-Kopie und Lockfile erneuern)

## [0.4.1] — 2026-10-09

- **Doku:** `AGENTS.md` legt fest: je App genau ein UI-System; fehlende Bausteine kommen in
  den Kit.

App-Aktion: keine

## [0.4.0] — 2026-10-09

- **Neu:** `AppShell` — gemeinsame Kopfleiste (Marke mit Home-Link, Aktionen, Steuerelemente)
  mit optionaler Navigation links und Detailbereich rechts, Sprunglink zum Inhalt,
  `renderLink` für Router-Links. Konstante `APP_SHELL_CONTENT_ID`.
- **Neu:** `SidePanel` (links/rechts, Breiten sm/md/lg, `--sidebar-*`-Tokens, optionaler
  Schließen-Knopf mit Fokus-Rückgabe), `PageContainer` (md/lg/full), `LanguageToggle`
  (Sprachkürzel im zugänglichen Namen).
- **Neu:** `themeInitScript(storageKey, defaultTheme)` setzt das Theme vor dem React-Start
  (kein Aufblitzen); `themeStorage` liest/schreibt das Theme abgesichert.
- **Geändert:** `theme.css` setzt `--font-sans` auf Noto Sans mit System-Fallback; die Schrift
  selbst lädt die App per `@fontsource-variable/noto-sans` (optionale Peer-Abhängigkeit).
- **Geändert:** `ThemeProvider` verwirft ungültige gespeicherte Werte und fällt auf den
  Standard zurück, statt sie als Klasse zu setzen.

App-Aktion: optional; Umbau je App: Kopfleiste/Panels auf `AppShell`/`SidePanel`/`PageContainer`
umstellen, eigene Theme-/Sprachumschalter durch die Kit-Varianten ersetzen, `themeInitScript`
in `index.html` einbinden, Noto Sans importieren (README „App-Shell").

## [0.3.2] — 2026-10-09

- **Neu (Tooling):** Release-Prozess. `VERSION` als einzige Kit-Version, beide
  `package.json` auf 0.3.2 angeglichen (vorher Backend 0.3.0, Frontend 0.3.1).
- **Neu (Tooling):** `CHANGELOG.md` mit „App-Aktion:"-Zeile je Eintrag, Historie ab 0.1.0.
- **Neu (Tooling):** `scripts/tag-release.sh` — taggt nur, wenn HEAD == `origin/main`,
  Arbeitsbaum sauber, Tag noch nicht vergeben, `VERSION`, `package.json`-Versionen und
  CHANGELOG-Eintrag passen.
- **Neu (Tooling):** `scripts/check-kit-version.sh` — vergleicht aus einem App-Repo den Pin
  von `vendor/web-app-kit` mit dem neuesten Kit-Tag und zeigt die CHANGELOG-Einträge dazwischen.
- Code der Pakete unverändert. Wer den Pin trotzdem anhebt: danach Kopie und Lockfile
  erneuern (README „Nutzung").

App-Aktion: keine

## [0.3.1] — 2026-10-09

- **Fix:** Hover relativ zur Fläche (`--surface-hover`, `--surface-selected`,
  `--surface-selected-hover`): `outline` in Ruhe transparent, Hover 8 %, gedrückt 14 %/20 %;
  `TableRow` mit Surface-Hover/-Auswahl; `--muted-foreground` Hell 0.50.
- **Neu:** `Card interactive asChild`, `CardProps`/`CardSectionProps`,
  `INTERACTIVE_SURFACE_CLASSES`, `STRETCHED_CARD_CLASSES`, `STRETCHED_LINK_CLASSES`.
- **Fix:** Fokus-Darstellung im Forced-Colors-Modus.
- **Fix (Build):** `fastify` 5.10.0 als Root-devDependency gehoben, damit die
  `@fastify/static`-Typen greifen (PR #4). Backend-Paketversion blieb 0.3.0.

App-Aktion: optional; lokale Card-Hover in mockup und workbench durch `Card interactive asChild`
bzw. `INTERACTIVE_SURFACE_CLASSES` ersetzen, eigene Hover-Werte durch `hover:bg-surface-hover`
(README „Migration je App", „Bump auf 0.3.1").

## [0.3.0] — 2026-10-07

- **Neu:** UI-Basis `@web-app-kit/frontend/ui` (shadcn, Tabler-Icons): badge, button, card,
  dialog, dropdown-menu, input, label, select, table, textarea, tooltip, `ThemeToggle`, `cn`.
- **Neu:** `applyThemeOverrides`; Tokens in `theme.css` auf WCAG 2.2 AA (Hell und Dunkel),
  neue Tokens `--brand`/`--brand-foreground`.
- **Neu (Backend):** `registerStaticSite`, `isServerPath`, `registerShutdown`,
  `ConfigurationError`.
- **Breaking:** `applyAccentOverride(oklchValue)` setzt nur noch `--brand`; der Parameter
  `variableNames` entfällt, `--accent`/`--ring` werden nicht mehr umgefärbt.
- **Optisch durch die geteilte `theme.css`:** `--input` in Dunkel deckend,
  dunklere Eingabe-Rahmen (`--input`), kräftigere Fokusringe, `--accent` in Hell 0.93.

App-Aktion: Pflicht (als MINOR-Sprung ausgeliefert, enthält aber einen Breaking Change).
Umbau-Liste:
- Instanzfarbe aus `applyAccentOverride` dort, wo sie sichtbar sein soll, über `bg-brand`,
  `text-brand` oder `border-brand` setzen (taste, splitter).
- App-lokale shadcn-Kopien mit `dark:bg-input/30` (`/50`, `/80`) auf Kit-Werte senken.
- Komponenten-Umstellung je App laut README „Migration je App" (empfohlen, nicht erzwungen).

## [0.2.1] — 2026-10-07

- **Fix (Backend):** ALB-Identität: fehlender Header `x-amzn-oidc-data` ergibt `null`
  (anonym) statt `UnauthorizedError`; ein vorhandener, ungültiger Header bleibt 401.

App-Aktion: optional; Checkliste für Apps mit ALB-Identität:
- Wo ein Login nötig ist, `null` vom Resolver als nicht angemeldet behandeln (z. B.
  `UnauthorizedError` werfen), statt sich auf den bisherigen 401 des Resolvers zu verlassen.
- Security-Group der App nur für den ALB und berechtigte interne Aufrufer öffnen (README).

## [0.2.0] — 2026-10-07

- **Neu (Backend):** `createAlbIdentityResolver`, `albIdentityConfigFromEnv`,
  `createIdentityResolver`, `toAlbIdentity`, `ALB_IDENTITY_HEADER`, Typen `AlbIdentity*`;
  Signaturprüfung über `aws-jwt-verify`.
- **Neu (Backend):** `UnauthorizedError` (401).

App-Aktion: optional; ALB-Identität über den Resolver statt eigener Header-Auswertung.

## [0.1.0] — 2026-10-07

- Erste getaggte Fassung. Backend: `AppError`, `NotFoundError`, `BadRequestError`,
  `ValidationError`, `UpstreamError`, `ConflictError`, `registerErrorHandler`,
  `loadLocalEnv` (über `node:util` `parseEnv`), `requireEnv`, `parseCorsOrigins`.
- Frontend: `ThemeProvider`, `useTheme`, `applyAccentOverride`, `createApiClient`, `ApiError`,
  `describeError(error, fallback)` (Standard-Fallback „Something went wrong"),
  `theme.css` (neutrale shadcn-Tokens).

App-Aktion: keine (Ersteinbindung als Submodule).
