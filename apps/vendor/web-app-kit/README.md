# web-app-kit

Geteilter Kern für die Web-Tools (taste-web, layerboard, plattform, …) — analog zu
`macos-app-kit`/`android-app-kit` für die nativen Apps. Ein Fix hier → alle Tools ziehen
ihn per Version-Bump. Ersetzt das bisher pro Repo separat nachgebaute Fastify+Prisma /
React+Vite-Muster.

## Aufbau

**`packages/backend`** (`@web-app-kit/backend`) — für Fastify+Prisma-Backends:
- `AppError` + Subklassen (`NotFoundError`, `BadRequestError`, `ValidationError`,
  `UpstreamError`, `ConflictError`, `UnauthorizedError`,
  `TooManyRequestsError`) — Fehler mit echtem Statuscode und Klartext-Message
  statt generischem
  "Internal server error" für alles ≥500.
- `registerErrorHandler(app)` — verdrahtet das oben stehende Muster als Fastify
  `setErrorHandler`.
- `loadLocalEnv()` — lädt `.env` falls vorhanden, No-Op unter Docker Compose (Env-Vars
  kommen dann direkt vom Container).
- `requireEnv(name)`, `parseCorsOrigins(value, fallback)` — kleine Config-Helfer.
- `createAlbIdentityResolver(albIdentityConfigFromEnv())` — liest die Identität aus dem
  ALB-Header `x-amzn-oidc-data` (Entra-Login am ALB), prüft die Signatur über
  `aws-jwt-verify` und liefert `{ id, name, email }` (`id` = Claim `sub`). Ohne
  `ALB_ARN`/`ALB_OIDC_ISSUER`/`ALB_OIDC_CLIENT_ID` (lokal) liefert der Resolver `null`;
  mit Konfiguration ergibt ein fehlender Header ebenfalls `null` (anonym, z. B. interne
  Server-zu-Server-Aufrufe), ein vorhandener, aber ungültiger Header `UnauthorizedError` (401).
  Schutz vor direkten Aufrufen am ALB vorbei: Security-Group der App nur vom ALB und von
  berechtigten internen Diensten.
- `registerStaticSite(app, { staticDir, serverPaths })` (ab 0.3.0) — liefert das gebaute
  Frontend über `@fastify/static` 10.1.5 aus. `serverPaths` (Default `["/api"]`) sind
  Backend-Pfade: exakter Treffer oder Präfix gefolgt von `/` bzw. `?`; sie fallen nie auf die
  SPA zurück. Unbekannte Pfade: `GET`/`HEAD` mit `Accept: text/html` bekommen `index.html`,
  alles andere wirft `NotFoundError` (JSON-404 über `registerErrorHandler`). Caching:
  `/assets/*` `public, max-age=31536000, immutable`, `index.html` `no-cache`. Dotfiles werden
  abgewiesen (403; der `@fastify/static`-Default wäre `allow`). Setzt voraus, dass
  `registerErrorHandler` registriert ist (sonst kein JSON-Format für 404/403), und übernimmt
  `setNotFoundHandler` — die App setzt keinen eigenen. Fehlt `<staticDir>/index.html`, wirft
  der Aufruf sofort `ConfigurationError` (Start bricht ab statt später 404 zu liefern).
  `/assets/` wird relativ zu `staticDir` bestimmt; die 404-Meldung enthält den Pfad ohne
  Query-String. `serverPaths` mit abschließendem `/` werden normalisiert.
- `registerShutdown(app, { onClose, timeoutMs, exit })` (ab 0.3.0) — erstes `SIGTERM`/`SIGINT`
  → `app.close()`, danach `exit(0)`, bei Fehler Log + `exit(1)`. Hängt das Schließen länger als
  `timeoutMs` (Default 10000), folgt Log + `exit(1)` (Timer mit `unref`). Ein zweites Signal
  erzwingt sofort `exit(1)`. Beim ersten Signal werden die Listener entfernt; der Rückgabewert
  entfernt sie ebenfalls (Tests). `onClose` läuft als Fastify-`onClose`-Hook, z. B.
  `() => prisma.$disconnect()`; `exit` ist für Tests injizierbar (Default `process.exit`).

```ts
registerErrorHandler(app);
await app.register(apiRoutes, { prefix: "/api" });
await registerStaticSite(app, { staticDir, serverPaths: ["/api", "/health"] });
registerShutdown(app, { onClose: () => prisma.$disconnect() });
```

Ersetzt die identischen Kopien `backend/src/lib/staticSite.ts` und `shutdown.ts` in mockup,
layerboard, splitter, taste und workbench (splitter: `serverPaths` mit `/mcp`, `/health`,
`/s.js`, `/snippet`; den eigenen Prisma-`onClose`-Hook durch `onClose` von
`registerShutdown` ersetzen).

**`packages/frontend`** (`@web-app-kit/frontend`) — für React+Vite-Frontends:
- `ThemeProvider`/`useTheme` — Light/Dark/System, persistiert in `localStorage`.
- `themeInitScript(storageKey, defaultTheme)` (ab 0.4.0) — Inline-Skript gegen das
  Aufblitzen des falschen Themes; siehe Abschnitt „App-Shell".
- `applyAccentOverride(oklchValue)` — setzt ab 0.3.0 nur noch `--brand` (Markenfarbe aus einer
  Build-Env-Var, z. B. Arbeit vs. privat). `--brand` nutzt kein Fokus-, Hover- oder
  Item-Zustand, die AA-Werte bleiben also erhalten. Sichtbar wird die Farbe nur dort, wo die
  App sie ausdrücklich einsetzt (`bg-brand`, `text-brand`, `border-brand`).
- `applyThemeOverrides({ "--x": { light, dark } })` — ausdrückliches Opt-in, um andere Tokens
  zu überschreiben; verlangt je einen Wert für Hell und Dunkel (ein einzelner Inline-Wert kann
  nicht in beiden Modi AA erfüllen). Kontrast der Werte prüft die App selbst.
- `createApiClient(backendUrl)` + `describeError(error)` — Fetch-Wrapper mit
  `ApiError`/Statuscode-Handling, passend zum `registerErrorHandler`-Antwortformat.
  `describeError(error, fallback)` gibt nur die Meldung eines `ApiError` oder eines
  `UserFacingError` zurück, alles andere (Netzwerkfehler wie „Failed to fetch", JSON-Parse-Fehler,
  Programmfehler) ergibt den übergebenen Fallback. Eigene Meldungen für Nutzer (z. B. eine
  Eingabe-Validierung) wirft die App als `UserFacingError`.

- `@web-app-kit/frontend/ui` — geteilte shadcn-Basis (Tabler-Icons), `cn`, `ThemeToggle`;
  siehe Abschnitt „UI-Basis". Ab 0.4.0 außerdem `AppShell`, `SidePanel`, `PageContainer`,
  `LanguageToggle`; siehe Abschnitt „App-Shell".
- `@web-app-kit/frontend/theme.css` — neutrale Tokens (WCAG 2.2 AA in Hell und Dunkel).

## Nutzung

Der Kit hängt als Git-Submodule unter `<app>/vendor/web-app-kit`, gepinnt auf ein
Release-Tag:

```bash
git submodule add <kit-repo-url> vendor/web-app-kit
git -C vendor/web-app-kit checkout v0.4.0
```

Aktuelle Version: `VERSION`, Änderungen und nötige App-Aktion je Version: `CHANGELOG.md`.
Ob der Pin einer App veraltet ist (Exit 1, listet die Einträge ab dem Pin; holt dazu die
Kit-Tags per `git fetch --tags` ins Submodule, Arbeitsbaum und Pin bleiben unverändert):

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
```

Das Script liegt ab v0.3.2 im Submodule. Bei älterem Pin aus einem aktuellen Kit-Checkout mit
App-Pfad aufrufen: `bash <kit>/scripts/check-kit-version.sh <app>`.

```jsonc
// <app>/frontend/package.json bzw. <app>/backend/package.json:
"dependencies": {
  "@web-app-kit/frontend": "file:../vendor/web-app-kit/packages/frontend",
  "@web-app-kit/backend": "file:../vendor/web-app-kit/packages/backend"
}
```

```ini
# <app>/frontend/.npmrc und <app>/backend/.npmrc
install-links=true
```

Mit `install-links=true` kopiert npm das Paket nach `node_modules/@web-app-kit/*`, statt
einen Symlink anzulegen. Folgen:
- Peer-Dependencies (React, Radix, …) werden aus der App aufgelöst, nicht aus einem
  eventuellen `vendor/web-app-kit/node_modules` → keine doppelte React-Instanz.
- Nach einem Submodule-Bump Kopie und Lockfile erneuern, je Paket:

  ```bash
  rm -rf node_modules/@web-app-kit
  npm install @web-app-kit/frontend@file:../vendor/web-app-kit/packages/frontend
  npm ls @web-app-kit/frontend   # muss die neue Version zeigen
  ```

  (Backend analog mit `backend`.) Gemessen mit npm 11.13.0 beim Bump 0.3.0 → 0.3.1:
  `rm -rf node_modules/@web-app-kit && npm install` erneuert zwar die Kopie, der
  Lockfile-Eintrag bleibt aber auf `0.3.0` und `npm ls` meldet `0.3.0`. Erst
  `npm install @web-app-kit/<pkg>@file:…` schreibt `0.3.1` in `package-lock.json`. Ohne
  vorheriges `rm -rf` ließ dieser Befehl bei unveränderter Versionsnummer die alte Kopie
  liegen, deshalb beide Schritte. Die `file:`-Angabe in `package.json` bleibt unverändert.

## Versionierung und Update

### CHANGELOG-Format ab 1.0.0

Jeder Eintrag ab 1.0.0 sagt, was neu ist und was eine App umbauen muss. Pflicht sind die Blöcke
`### Neu` und `### Umbau` sowie die Zeile `App-Aktion: keine | optional | Pflicht …`;
`scripts/tag-release.sh` verweigert das Tag sonst, ebenso bei `App-Aktion: Pflicht` ohne konkreten
Umbau-Schritt. Einträge vor 1.0.0 bleiben im alten Format und werden weiter angezeigt.

| Teil | Inhalt |
| --- | --- |
| `### Neu` | je Punkt: was es gibt und wofür (Komponente, Funktion, Fix) |
| `### Umbau` | je Punkt ein konkreter Schritt in der App; bei `App-Aktion: keine` genügt `- keiner` |
| `Prüfen: \`<befehl>\`` | optional, eingerückt unter einem Umbau-Punkt: findet betroffene Stellen in der App |
| `App-Aktion:` | `keine`, `optional` oder `Pflicht` mit Kurzbegründung |

Vorlage:

```markdown
## [1.2.0] — 2026-12-01

### Neu

- `Switch` in `@web-app-kit/frontend/ui`: Ein/Aus-Schalter für Einstellungen.

### Umbau

- Lokale `components/ui/switch.tsx` löschen, Importe auf `@web-app-kit/frontend/ui` umstellen.
  Prüfen: `grep -rl "components/ui/switch" frontend/src`

App-Aktion: optional (lokale Switch-Kopien ersetzen)
```

Prüfbefehle laufen relativ zum Wurzelordner der App und ohne Shell: nur `grep`, `rg`, `find`,
`ls` oder `test`, Anführungszeichen wie in der Shell, keine Pipes, Umleitungen, Variablen oder
Globs (`; | & < > $` und Backtick sind verboten), keine ausführenden Optionen (`find -exec`,
`-delete`, `-fprint…`, `rg --pre`). Ausgabe des Befehls = betroffene Dateien, ohne Ausgabe =
nicht betroffen; bei `test` zählt der Exit-Code (0 = betroffen). Der Befehl muss also gerade die
Stellen finden, die noch umzubauen sind, z. B. `grep -rl`, `find … -name`, `test -f`.

### Update in der App

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
```

Bei veraltetem Pin (Exit 1) zeigt das Script die Einträge zwischen Pin und neuestem Tag
(neueste oben) und führt danach jeden `Prüfen:`-Befehl im App-Verzeichnis aus:

```text
Prüfbefehle aus den Umbau-Listen (ausgeführt in /pfad/zur/app):
  [1.2.0] grep -rl "components/ui/switch" frontend/src
    betroffen: frontend/src/settings.tsx
  [1.1.0] test -f frontend/src/legacy-theme.css
    nicht betroffen
  [1.1.0] grep -rl x frontend | wc -l
    nicht ausgeführt: erlaubt sind nur grep, rg, find, ls, test ohne Shell-Zeichen
    (; | & < > $ `) und ohne ausführende Optionen. Befehl von Hand prüfen.
```

Ein Fehler des Befehls (z. B. fehlender Ordner) erscheint als „Prüfung fehlgeschlagen: …“. Die
Exit-Codes hängen nicht von den Prüfergebnissen ab. Das Script stammt aus dem ausgecheckten
Submodule: nach `git -C vendor/web-app-kit checkout <neuer-tag>` erneut ausführen, dann zeigt es
die Umbau-Listen mit dem Script des neuen Tags (der committete Pin ist bis zum Commit noch der
alte). Jeden Umbau-Punkt einzeln umsetzen und den Prüfbefehl danach erneut laufen lassen; erst
bei „nicht betroffen“ ist der Punkt erledigt. Ablauf mit allen Schritten (Lockfile, Vite-Cache,
Container, Skills, Pin-Hook): Skill `web-app-kit-update`.

Nach erneuertem Lockfile bündelt Vite die Abhängigkeiten beim nächsten Start neu
(Cache `node_modules/.vite`). Ein laufender Dev-Server merkt das nicht: neu starten, bei
unveränderter Kit-Version mit `npx vite --force`.

## UI-Basis (`@web-app-kit/frontend/ui`, ab 0.3.0)

Geteilte shadcn-Primitives (Stil `radix-nova`, `baseColor neutral`, Tabler-Icons), `cn` und
`ThemeToggle`. Eigener Subpath, damit der Haupt-Einstieg `@web-app-kit/frontend`
(ThemeProvider, ApiClient) keine Radix-/Tabler-Abhängigkeit bekommt: Apps, die noch nicht
migriert sind, bauen nach dem Kit-Bump unverändert.

Enthalten: `Badge`, `Button`, `Card*`, `Dialog*`, `DropdownMenu*`, `Input`, `Label`, `Select*`,
`Table*`, `Textarea`, `Tooltip*`, `cn`, `ThemeToggle` (+ `badgeVariants`, `buttonVariants`),
ab 0.3.1 `Card interactive`, `asChild` an `Card`/`CardHeader`/`CardTitle`/`CardDescription`,
`INTERACTIVE_SURFACE_CLASSES`, `STRETCHED_CARD_CLASSES`, `STRETCHED_LINK_CLASSES`,
`Toggle`, `ToggleGroup*`, `Popover*` (+ `toggleVariants`, Abschnitt 8), `ContextMenu*`,
`Checkbox`, `Separator` (Abschnitt 9), `AlertDialog*`, `Tabs*`, `Switch`, `Progress`,
`Skeleton`, `Toaster`, `toast` (+ `tabsListVariants`, Abschnitt 10), `MetaRow` und die
`Badge`-Varianten `info` und `warning` (Farbrollen `--info`, `--warning`).

### 1. Abhängigkeiten der App

```bash
npm install @tabler/icons-react@3.49.0
```

Weitere Peer-Dependencies haben alle Web-Tools bereits: `react` ^19.0.0, `radix-ui` ^1.6.2,
`class-variance-authority` ^0.7.1, `clsx` ^2.1.1, `tailwind-merge` ^3.6.0.

`sonner` ist Pflicht, auch ohne `Toaster`: der Einstieg `@web-app-kit/frontend/ui` importiert es,
sonst scheitern `tsc -b` und der Build (`npm install --save-exact sonner@2.0.8`).

`cmdk` braucht nur, wer `Command` oder `MultiCombobox` nutzt: beide liegen im eigenen Einstieg
`@web-app-kit/frontend/command`, damit `/ui` ohne `cmdk` baut
(`npm install --save-exact cmdk@1.1.1`).

### 2. CSS

In `<app>/frontend/src/index.css`:

```css
@import "tailwindcss";
@import "tw-animate-css";
@import "shadcn/tailwind.css";
@import "@web-app-kit/frontend/theme.css";
@source "../node_modules/@web-app-kit/frontend/src";
```

`@source` ist relativ zur CSS-Datei. Mit `install-links=true` wird die Kopie in
`node_modules` gebündelt; genau diese Quelle scannen. Tailwind ignoriert `node_modules` bei
der automatischen Erkennung — verifiziert mit einer Vite-8.1.4-/Tailwind-4.3.2-Demo im
Submodule-Layout: ohne `@source` fehlten die Kit-Klassen im Production-Build (im Dev-Server
waren sie vorhanden, deshalb fällt der Fehler erst im Build auf). Mit
`@source "../node_modules/@web-app-kit/frontend/src"` und ebenso mit
`@source "../../vendor/web-app-kit/packages/frontend/src"` waren sie vollständig. Empfohlen
ist der `node_modules`-Pfad, weil er immer zur gebündelten Kopie passt, auch wenn das
Submodule schon weiter ist als die installierte Kopie.

`theme.css` liefert alle Tokens, die die Komponenten brauchen (inkl. `--radius-2xl..4xl`
und `--font-heading`). `tw-animate-css` und `shadcn/tailwind.css` liefern
`animate-in`/`data-open:`-Varianten und bleiben App-Imports. Eine App mit eigenen Tokens statt
`theme.css` (splitter) braucht dieselben Token-Namen.

### 3. Vite

Mit `install-links=true` ist kein `resolve.dedupe` nötig (Demo: React, Radix und Tabler
kamen ausschließlich aus `<app>/frontend/node_modules`). Nur wenn der Kit als Symlink
eingebunden ist (`file:` ohne `install-links`) und der Kit-Checkout eigene `node_modules`
hat, landet React doppelt im Bundle (`Invalid hook call`, in der Demo reproduziert). Dann:

```ts
resolve: {
  dedupe: ["react", "react-dom", "radix-ui", "@tabler/icons-react",
    "class-variance-authority", "clsx", "tailwind-merge"],
},
```

### 4. ThemeToggle

Ein Icon-Button, Zyklus System → Hell → Dunkel, nutzt `useTheme` des Kits. Labels kommen
ausschließlich aus der App, es gibt keinen Default-Text:

```tsx
import { ThemeToggle } from "@web-app-kit/frontend/ui";

<ThemeToggle
  labels={{ system: t("theme.system"), light: t("theme.light"), dark: t("theme.dark") }}
  describe={(label) => t("theme.toggleTitle", { label })}
/>
```

`describe` ist Pflicht: es formuliert aus dem aktuellen Modus das `aria-label`/`title`, das
die Aktion beschreibt (z. B. „Darstellung: System – klicken zum Wechseln"); ein nacktes
„System" sagt Screenreader-Nutzern nicht, was der Button tut. `variant`/`size` sind
überschreibbar (Default `ghost`/`icon`, 32 px Ziel).

### 5. Komponenten-Verhalten

- **Dialog:** `DialogContent` verlangt `closeLabel`, solange der Schließen-Button aktiv ist
  (`showCloseButton={false}` hebt die Pflicht auf); `DialogFooter showCloseButton` verlangt
  ebenfalls `closeLabel`. Der Typ erzwingt das.
- **Button:** `disabled:cursor-not-allowed` (Hover/Active greifen nur bei `not-disabled`),
  `aria-haspopup`-Buttons quittieren den Klick mit `opacity-80` statt Verschiebung.
  Outline-Rahmen nutzt `--input` (3:1).
- **Hover/gedrückt (Zustandsmodell an der Primärfarbe, ab 0.10.0):** `outline` und `ghost`
  sind in Ruhe transparent (Outline behält den `border-input`-Rahmen). Hover ist zart:
  `bg-primary-subtle` mit `text-primary-subtle-foreground`. Gedrückt (`:active`),
  `aria-pressed` und `aria-expanded` (z. B. geöffneter Popover- oder Menü-Trigger) zeigen
  Primärrahmen plus 1-px-Innenring (`border-primary`, `inset-ring-1 inset-ring-primary`) auf
  `bg-primary-subtle`; alle Nachfahren erhalten `text-primary-subtle-foreground`. Der Ring
  liegt innen (`inset-ring`), damit der Fokusring (`ring-3 ring-ring/50`) bei gedrückten
  Elementen erhalten bleibt. `default` hovert mit `--primary-hover` und wird gedrückt
  `--primary-active`. `ThemeToggle`, Dialog-Schließen-Button und Select-Trigger übernehmen
  das Modell (`PRIMARY_SUBTLE_STATE_CLASSES` in `ui/interaction-states.ts`). `TableRow` und
  der Skip-Link nutzen weiterhin `--surface-hover`/`--surface-selected` (Utilities
  `bg-surface-hover`, `bg-surface-selected`, `bg-surface-selected-hover`, `overlay-hover`).
  `secondary` hovert mit `--secondary-hover` und wird gedrückt `--secondary-active` (ab 0.12.0;
  Default: 8 % bzw. 14 % `--foreground` auf `--secondary`). `tertiary` (ab 0.12.0) ist die
  neutrale Fläche `--tertiary` mit `--tertiary-hover`, gedrückt `--tertiary-active` und
  `--tertiary-active-foreground`. Dunkel-`destructive` hovert mit `/26`.
  Menü- und Select-Items: Fokus/Hover `focus:bg-primary-subtle` plus innerer 2-px-Ring in
  `--ring`, ausgewählt per Check-Icon in `text-primary`. Link-Badges (`Badge asChild` mit `<a>`)
  quittieren den Klick wie Buttons mit `translate-y-px` (`[a]:active:translate-y-px`); Hover
  kommt je Variante über `[a]:hover:…` bzw. bei `link` über die Unterstreichung, der Fokusring
  über `focus-visible:ring-[3px]`.
  Die folgende Messung stammt aus 0.3.1 (Graustufen-Overlay) und gilt noch für `secondary`,
  `destructive` und `TableRow`; die Werte ab 0.10.0 stehen unter „Primärfarbe".
  Gemessen (Playwright, Chromium; System-Modus und per `ThemeToggle` gesetzte Klasse identisch),
  Flächen-Kontrast Hover zu Ruhe:

  | Variante | Hell Seite | Hell Card/Popover | Dunkel Seite | Dunkel Card | Dunkel Popover |
  | --- | --- | --- | --- | --- | --- |
  | `outline`, `ghost` | 1.18 | 1.18 | 1.22 | 1.27 | 1.24 |
  | `aria-pressed` + Hover | 1.15 | 1.15 | 1.24 | 1.22 | 1.23 |
  | `secondary` | 1.22 | 1.22 | 1.22 | 1.22 | 1.22 |
  | `destructive` | 1.20 | 1.20 | 1.17 | 1.15 | 1.17 |
  | `default` | 1.80 | 1.86 | 1.52 | 1.46 | 1.51 |
  | `Card interactive`, Stretched-Card, Zeilen | – | 1.18 | – | 1.27 | – |
  | `TableRow` Hover | 1.18 | 1.18 | 1.22 | 1.27 | – |
  | `TableRow` ausgewählt zu Hover | 1.16 | 1.16 | 1.21 | 1.22 | – |
  | `TableRow` ausgewählt + Hover | 1.15 | 1.15 | 1.24 | 1.22 | – |

  Text bleibt in Ruhe und bei Hover ≥ 4.5:1 (niedrigster Wert: `destructive` auf Dunkel-Card
  4.62, `Card interactive` Sekundärtext Hell 5.09). `link` zeigt Hover per Unterstreichung.
  Eigene interaktive Elemente in Apps nicht mit `hover:bg-muted` oder eigenem `color-mix`
  bauen: In Hell sind `--muted` und `--background` gleich, auf einer weißen Card ergab ein
  dunkles `/6` nur 1.04:1.
- **Interaktive Karte und Flächen (ab 0.3.1):** Zwei Muster, beide mit Hover
  `bg-primary-subtle` (ab 0.10.0, vorher `overlay-hover`), gedrückt `translate-y-px` wie bei
  Buttons. Ausgewählt (`aria-selected="true"`, `aria-current="page"`/`"true"`,
  `data-selected`): `bg-primary-subtle` plus 2-px-Innenring `inset-ring-primary`.

  **a) Ganze Karte ist ein Link oder eine Aktion:** `Card interactive asChild` mit `<a>` oder
  `<button type="button">` als Kind. Der Typ verlangt `asChild` zusammen mit `interactive`,
  damit Rolle und Tastaturbedienung vom nativen Element kommen (Tab, Enter bzw.
  Enter/Leertaste). Die Karte bekommt `INTERACTIVE_SURFACE_CLASSES`: `cursor-pointer`,
  Fokusring `focus-visible:ring-2 ring-ring` (voll deckend, ab 0.11.0; vorher
  `ring-3 ring-ring/50` mit nur 2.36–2.48:1 bei kräftiger Primärfarbe),
  `focus-visible:outline-hidden` (im Forced-Colors-Modus bleibt ein 2-px-Fokusrahmen sichtbar,
  gemessen per `emulateMedia({ forcedColors: "active" })`), `disabled:` wie Buttons und
  `aria-disabled:pointer-events-none aria-disabled:opacity-50` für deaktivierte Link-Karten
  (Tastatur-Aktivierung verhindert die App, z. B. kein `href`). Ein `<button>` darf nur
  Phrasing-Content enthalten: `CardHeader`, `CardTitle` und `CardDescription` dort per
  `asChild` als `<span>` rendern. Bei längerem Inhalt Namen und Beschreibung per
  `aria-labelledby`/`aria-describedby` setzen, sonst wird der gesamte Text zum Namen.

  ```tsx
  <Card interactive asChild>
    <button type="button" onClick={open} aria-labelledby={titleId} aria-describedby={descId}>
      <CardHeader asChild>
        <span>
          <CardTitle asChild><span id={titleId}>{name}</span></CardTitle>
          <CardDescription asChild><span id={descId}>{summary}</span></CardDescription>
        </span>
      </CardHeader>
    </button>
  </Card>

  <Card interactive asChild>
    <a href={`/projects/${id}`}>
      <CardHeader><CardTitle>{name}</CardTitle></CardHeader>
    </a>
  </Card>
  ```

  Keine weiteren interaktiven Elemente (Buttons, Links, `CardAction` mit Button) in einer
  Karte nach Muster a): verschachtelte interaktive Elemente sind in `<a>`/`<button>` nicht
  zulässig und für Tastatur und Screenreader nicht bedienbar. Dafür Muster b).

  **b) Karte mit eigenen Aktionen (Stretched Link):** Die Karte bleibt `div`, der Titel enthält
  den Link, dessen `::after` die ganze Karte abdeckt (`STRETCHED_LINK_CLASSES`:
  `after:absolute after:inset-0`, Fokusring als innerer 2-px-Ring in `--ring` auf dem
  `::after`, gemessen 19.8:1 Hell / 10.2:1 Dunkel gegen die Card). `STRETCHED_CARD_CLASSES`
  gibt der Karte `relative`, Hover und gedrückt, außer wenn Zeiger bzw. Druck auf der
  `CardAction` liegen. Aktionen liegen per `relative z-10` über dem Link und sind eigene
  Tab-Stopps.

  ```tsx
  <Card className={STRETCHED_CARD_CLASSES}>
    <CardHeader>
      <CardTitle>
        <a href={`/projects/${id}`} className={STRETCHED_LINK_CLASSES}>{name}</a>
      </CardTitle>
      <CardDescription>{summary}</CardDescription>
      <CardAction className="relative z-10">
        <Button size="sm" variant="outline">{t("archive")}</Button>
      </CardAction>
    </CardHeader>
  </Card>
  ```

  **Listenzeilen und eigene Kacheln:** `INTERACTIVE_SURFACE_CLASSES` direkt auf das
  `<button>`/`<a>` legen, z. B.
  `className={cn(INTERACTIVE_SURFACE_CLASSES, "w-full rounded-md px-3 py-2")}`.

  Hinweise: Die drei Konstanten sind Strings (unveränderlich, direkt in `className` oder `cn`
  nutzbar). Hover ersetzt die Hintergrundfarbe der Fläche durch `--primary-subtle`. Klassen bei
  `asChild` an `Card` übergeben, nicht an das Kind: Slot hängt die Kind-Klassen ungeprüft an,
  `cn`/`tailwind-merge` löst Konflikte nur für die an `Card` übergebenen Klassen.
- **Markierter Eintrag in Menüs und Select (ab 0.11.0):** `--primary-subtle` hebt sich nur
  1.17–1.54:1 vom Popover ab. Der markierte Eintrag (`data-highlighted`, per Tastatur und per
  Zeiger) trägt deshalb zusätzlich einen inneren 2-px-Ring in `--ring`
  (`MENU_HIGHLIGHT_CLASSES` in `ui/menu-classes.ts`: `data-highlighted:inset-ring-2
  inset-ring-ring`), gilt für `DropdownMenu*`, `ContextMenu*` und `SelectItem`. Gemessen
  (deep pink sample): 4.38:1 Hell / 4.76:1 Dunkel gegen die Item-Fläche, 5.14 / 5.89 gegen das
  Popover; `destructive` 4.32 / 3.83. `DropdownMenuSubTrigger` trägt die Gruppe
  `group/dropdown-menu-item`, damit `DropdownMenuShortcut` darin
  `primary-subtle-foreground` übernimmt (analog `group/context-menu-item`).
- **Select:** Standard ist `position="item-aligned"` mit `IconSelector` im Trigger.
  Popover-Verhalten über `position="popper"` an der Aufrufstelle.

### 6. Tokens und WCAG 2.2 AA

`theme.css` erfüllt in Hell und Dunkel 4.5:1 für Text und 3:1 für Eingabe-Rahmen und
Fokusring (gemessen per Playwright, System-Modus und per ThemeToggle gesetzte Klasse).

Fokusindikator ≥ 3:1 (ab 0.11.0): Ein Halo `ring-ring/50` allein erreicht mit kräftiger
Primärfarbe nur 2.2–2.6:1. Elemente ohne eigenen 1-px-Rahmen in `--ring` zeigen den Fokus
deshalb voll deckend: `INTERACTIVE_SURFACE_CLASSES` (`ring-2 ring-ring`), `STRETCHED_LINK_CLASSES`
(innerer Ring), Skip-Link (`ring-3 ring-ring`), `Checkbox` (Outline 2 px mit 2 px Abstand) und
die Basis-Outline aller Elemente (`outline-ring` statt `outline-ring/50` in `theme.css`).
`src/active-state.test.ts` prüft `--ring` gegen `--background`, `--card`, `--popover`,
`--sidebar` und `--primary-subtle` (≥ 3:1, Hell und Dunkel, inkl. deep pink sample).
Angehoben gegenüber 0.2.x: `--destructive`, `--input`, `--ring`/`--sidebar-ring`,
`--muted-foreground`, `--accent` (nur Hell). Neu: `--brand`/`--brand-foreground`
(Utilities `bg-brand`, `text-brand`, `text-brand-foreground`), Default neutral wie
`--primary`.

`applyAccentOverride` schreibt seit 0.3.0 nur `--brand`. Bis 0.2.x überschrieb es `--accent`
und `--ring` mit einem einzigen Inline-Wert für beide Modi; gemessen ergab das Fokusringe von
1.5–2.7:1 und Item-Text auf Accent von 2.4–3.3:1, also unter AA. Wer andere Tokens umfärben
will, nutzt `applyThemeOverrides` mit `{ light, dark }` und weist ≥ 4.5:1 (Text) bzw. ≥ 3:1
(Ring/Rahmen) in beiden Modi selbst nach. `--brand-foreground` wird von
`applyAccentOverride` nicht angepasst; Text auf `bg-brand` braucht eine eigene Prüfung.

### 7. Migration je App

Allgemein: Importe der Kit-Komponenten von `@/components/ui/<x>` auf
`@web-app-kit/frontend/ui` umstellen, lokale Kopien löschen, `cn` aus dem Kit importieren
(oder `lib/utils.ts` als Re-Export behalten), `components.json` auf `"iconLibrary": "tabler"`,
lucide durch Tabler ersetzen, `@source` ergänzen.

**Bump auf 0.3.1** (nur Frontend; Backend bleibt 0.3.0):
- `outline` ist in Ruhe transparent statt `bg-background` (Hell) bzw. `bg-input/10` (Dunkel);
  auf Cards und Popovers übernimmt er jetzt deren Fläche.
- Hover von `outline`/`ghost`/`ThemeToggle`/Select-Trigger/Link-Badges 8 % statt 6 %,
  `aria-pressed`/`aria-expanded` 14 % statt 12 % (Dunkel-Outline 16 %), gedrückt + Hover 20 %.
- `--muted-foreground` Hell 0.50 statt 0.53: Sekundärtext hält damit auch auf Hover-Flächen
  4.5:1 (mit 0.53 und 8 % Hover gerechnet 4.08 auf der Seite, 4.44 auf einer Card).
- `outline`-Button und Select-Trigger in Dunkel: die Ruhe-Füllung `bg-input/10` und der Hover
  `bg-input/30` entfallen; beide sind in Ruhe transparent und hovern mit `--surface-hover`.
- `TableRow`: Hover `bg-surface-hover` statt `bg-muted/50` (auf weißer Card vorher kaum
  sichtbar), ausgewählt bzw. `has-aria-expanded` `bg-surface-selected` statt `bg-muted`
  (`/50`), ausgewählt + Hover `bg-surface-selected-hover`; Text in ausgewählten Zeilen und in
  gedrückten Buttons ist `text-foreground` (auch Zellen mit `text-muted-foreground`).
- Apps: lokale Card-Hover (`hover:bg-[color-mix(...)]`, `hover:bg-foreground/6`,
  `hover:bg-muted`) in mockup und workbench durch `Card interactive asChild` bzw.
  `INTERACTIVE_SURFACE_CLASSES` ersetzen. Eigene Hover-Werte auf transparenten Elementen durch
  `hover:bg-surface-hover` ersetzen.

**Optische Änderungen allein durch den Bump auf 0.3.0** (auch ohne Komponenten-Umstellung,
weil `theme.css` geteilt ist):
- `--input` ist in Dunkel jetzt deckend `oklch(0.58 0 0)` statt `white / 15%`. App-lokale
  shadcn-Kopien mit `dark:bg-input/30` (bzw. `/50`, `/80`) wirken dadurch deutlich heller;
  dort auf `/10`, `/17`, `/28` senken (Kit-Werte), in Hell `disabled:bg-input/50` → `/7`.
- Rahmen von Inputs/Selects/Outline-Buttons sind dunkler (3:1), Fokusringe kräftiger
  (`--ring` Hell 0.145, Dunkel 0.87), `--destructive` in Hell dunkler, in Dunkel heller.
- `--accent` in Hell 0.93 statt 0.97: Hover-/Fokusflächen in Menüs sind sichtbarer.
- `--muted-foreground` Hell 0.53 / Dunkel 0.76: Sekundärtext minimal kontrastreicher.
- `applyAccentOverride` färbt `--accent`/`--ring` nicht mehr: taste und splitter rufen es mit
  `VITE_ACCENT_OKLCH` auf. Ihre Instanz-Farbe liegt danach nur noch auf `--brand`. Wo sie
  sichtbar sein soll (z. B. Header-Leiste, Logo, aktive Navigation), dort `bg-brand`,
  `text-brand` oder `border-brand` setzen. Das bisherige Einfärben von Menü-Fokus und
  Fokusring entfällt bewusst (AA).

| App | Ersetzen durch Kit | Bleibt lokal | Besonderheiten |
| --- | --- | --- | --- |
| layerboard | badge, button, card, dialog, dropdown-menu, input, label, select, table, textarea, tooltip | – | `closeLabel` an allen `DialogContent`; 23 Dateien mit lucide; Theme-Auswahl im SettingsPanel kann bleiben, `ThemeToggle` im Header ergänzen |
| mockup | button, card, dialog, input, label, select, textarea, popover (ab 0.10.0) | context-menu | eigener `theme-provider.tsx` → Kit; `border-black` am Default-Button entfällt (Tokens); `aria-pressed`-Zustand jetzt Kit-Standard; `ThemeToggle.tsx` ersetzen (Zyklus ändert sich auf System → Hell → Dunkel) |
| workbench | button, tooltip | – | `ThemeToggle.tsx` ersetzen; `size="icon-sm"` übergeben, um die bisherige Größe zu halten |
| splitter | badge, button, card, dialog, dropdown-menu, input, label, select, table, textarea, tooltip | alert-dialog, chart, progress, separator, skeleton, sonner, switch, tabs | Dropdown-Theme-Umschalter durch Kit-`ThemeToggle` ersetzen, Labels per i18n statt fest „Hell/Dunkel"; eigene Tokens (orange `--accent`/`--ring`) auf AA prüfen; `VITE_ACCENT_OKLCH` landet auf `--brand` |
| taste | badge, button, dialog, input, label, select | checkbox | Select-Default war `position="popper"` → an den Aufrufstellen `position="popper"` setzen oder `item-aligned` übernehmen; `closeLabel={t("common.dialogCloseAriaLabel")}`; `ThemeToggle.tsx` ersetzen; `VITE_ACCENT_OKLCH` landet auf `--brand` (`.impression-card-selected` nutzt `var(--accent)` → auf `var(--brand)` umstellen, falls die Instanzfarbe dort bleiben soll) |

### 8. Werkzeugleisten: Toggle, ToggleGroup, Popover

Bausteine für Werkzeugleisten, in denen das aktive Werkzeug auf einen Blick erkennbar sein
muss (z. B. Layout-Editor), und für kleine Einstellungs-Popups. Basis sind die Radix-Primitives
aus `radix-ui` (bereits Peer-Dependency), keine neue Abhängigkeit.

- **`Toggle`:** Umschalter mit eigenem An/Aus-Zustand (`pressed`/`defaultPressed`,
  `onPressedChange`). Radix setzt `aria-pressed` und `data-state="on"|"off"`. Varianten
  `default` (in Ruhe transparent) und `outline` (Rahmen `--input`), Größen wie `Button`:
  `default`, `xs`, `sm`, `lg`, `icon`, `icon-xs`, `icon-sm`, `icon-lg`.
- **Aktiv-Zustand:** Fläche `--primary`, Schrift `--primary-foreground` (auch für alle
  Nachfahren), bei `outline` zusätzlich Rahmen `--primary`. Damit folgt der Aktiv-Zustand der
  Primärfarbe aus `brand.css` bzw. `applyThemeOverrides`. Aus: Hover `bg-primary-subtle`,
  gedrückt Primärrahmen plus Innenring auf `bg-primary-subtle`. An: Hover `--primary-hover`,
  gedrückt `--primary-active` (eine `/80`-Deckkraft hätte den Text-Kontrast bei kräftigen
  Primärfarben unter 4.5:1 gesenkt). Gedrückt zusätzlich `translate-y-px`, Fokus
  `focus-visible:ring-3 ring-ring/50`, deaktiviert `cursor-not-allowed opacity-50`.
- **`ToggleGroup` + `ToggleGroupItem`:** `type="single"` (genau ein aktives Werkzeug, Radix
  rendert `role="radiogroup"` mit `role="radio"`/`aria-checked` je Item) oder
  `type="multiple"` (unabhängige Schalter, `role="toolbar"` mit `aria-pressed` je Item).
  Tastatur aus der Primitive: ein Tab-Stopp für die Gruppe (Roving Focus), Pfeiltasten wechseln
  das Item, Leertaste/Enter schaltet. `orientation` (`horizontal`, `vertical`) steuert auch die
  Pfeiltasten-Richtung. `variant` und `size` an der Gruppe gelten für alle Items, `spacing={0}`
  fügt die Items zu einem Block zusammen. Der Typ verlangt `label` (wird `aria-label`) oder
  `aria-labelledby`; Texte kommen aus der App.
- **`Popover`:** `Popover`, `PopoverTrigger`, `PopoverContent`, `PopoverAnchor`,
  `PopoverHeader`, `PopoverTitle`, `PopoverDescription`. Fläche wie `DropdownMenuContent`
  (`bg-popover`, `ring-foreground/10`, `shadow-md`, `rounded-lg`), Breite `w-72`, Höhe auf den
  verfügbaren Platz begrenzt. Fokus geht beim Öffnen in den Inhalt, Escape und Klick außerhalb
  schließen, der Fokus kehrt zum Trigger zurück (Radix-Verhalten, nicht überschreiben). Trigger
  per `asChild` auf `Button` oder `Toggle`.
- **A11y:** Icon-only-Items brauchen ein `aria-label` aus i18n; ein `Tooltip` ergänzt, ersetzt
  es aber nicht. Aktiv ist nie nur Farbe: Zustand liegt in `aria-checked`/`aria-pressed`.
  Kontrast-Nachweis in `src/active-state.test.ts` (Hell und Dunkel, Werte aus `brand.css` und
  ein kräftiges Pink als zweiter Fall), Paare siehe „Primärfarbe".

Werkzeugleiste mit aktivem Werkzeug und Einstellungs-Popup:

```tsx
import { IconPointer, IconSquare, IconTypography, IconAdjustments } from "@tabler/icons-react";
import {
  Button,
  Popover,
  PopoverContent,
  PopoverTitle,
  PopoverTrigger,
  ToggleGroup,
  ToggleGroupItem,
} from "@web-app-kit/frontend/ui";

<div className="flex items-center gap-2">
  <ToggleGroup
    type="single"
    label={t("editor.tools")}
    value={tool}
    onValueChange={(next) => next && setTool(next)}
    variant="outline"
    size="icon"
  >
    <ToggleGroupItem value="select" aria-label={t("editor.toolSelect")}>
      <IconPointer aria-hidden />
    </ToggleGroupItem>
    <ToggleGroupItem value="frame" aria-label={t("editor.toolFrame")}>
      <IconSquare aria-hidden />
    </ToggleGroupItem>
    <ToggleGroupItem value="text" aria-label={t("editor.toolText")}>
      <IconTypography aria-hidden />
    </ToggleGroupItem>
  </ToggleGroup>
  <Popover>
    <PopoverTrigger asChild>
      <Button variant="ghost" size="icon" aria-label={t("editor.gridSettings")}>
        <IconAdjustments aria-hidden />
      </Button>
    </PopoverTrigger>
    <PopoverContent align="start">
      <PopoverTitle>{t("editor.gridSettings")}</PopoverTitle>
      {/* Felder mit Label + Input */}
    </PopoverContent>
  </Popover>
</div>
```

`onValueChange={(next) => next && setTool(next)}` verhindert, dass ein erneuter Klick auf das
aktive Werkzeug die Auswahl leert (Radix liefert dann `""`).

### 9. Kontextmenü, Checkbox, Separator (ab 0.11.0)

shadcn-Form auf den Radix-Primitives aus `radix-ui` (bereits Peer-Dependency), keine neue
Abhängigkeit. Keine eingebauten Texte, Beschriftungen kommen aus der App.

- **`ContextMenu`:** `ContextMenu`, `ContextMenuTrigger`, `ContextMenuContent`,
  `ContextMenuItem` (`variant` `default`/`destructive`, `inset`), `ContextMenuCheckboxItem`,
  `ContextMenuRadioGroup`, `ContextMenuRadioItem`, `ContextMenuLabel`, `ContextMenuSeparator`,
  `ContextMenuShortcut`, `ContextMenuGroup`, `ContextMenuPortal`, `ContextMenuSub`,
  `ContextMenuSubTrigger`, `ContextMenuSubContent`. Fläche, Items und Zustände wie
  `DropdownMenu` (gemeinsame Klassen in `ui/menu-classes.ts`). Öffnen per Rechtsklick und per
  Tastatur: `ContextMenuTrigger` öffnet bei Shift+F10 und der Kontextmenü-Taste an seiner
  linken unteren Ecke (eigene Tastaturbehandlung, unabhängig vom Browser), der erste
  Eintrag erhält den Fokus, Escape schließt und gibt den Fokus an den Trigger zurück. Der
  Trigger muss dafür fokussierbar sein (`tabIndex={0}` oder `asChild` auf ein fokussierbares
  Element) und braucht einen sichtbaren Fokus (Basis-Outline in `--ring`). Ein eigenes
  `onKeyDown` mit `event.preventDefault()` schaltet das Öffnen per Tastatur ab. Jede Aktion des
  Kontextmenüs muss auch ohne Rechtsklick erreichbar sein (Werkzeugleiste, `DropdownMenu`).
- **`Checkbox`:** `checked`/`defaultChecked` (`true`, `false`, `"indeterminate"`),
  `onCheckedChange`. Radix setzt `role="checkbox"`, `aria-checked` (`mixed` bei
  `indeterminate`) und `data-state`. Aus: Rahmen `--input` (3.34:1 Hell / 4.29:1 Dunkel mit
  deep pink sample), Hover `bg-primary-subtle` mit Rahmen `--primary`, gedrückt zusätzlich
  1-px-Innenring. An und `indeterminate` (Strich-Icon): Fläche `--primary`, Icon
  `--primary-foreground`, Hover `--primary-hover`, gedrückt `--primary-active`. Fokus:
  Outline 2 px in `--ring` mit 2 px Abstand, damit er auch auf der Primärfläche sichtbar
  bleibt (deep pink sample: 4.71–6.05:1 gegen die Seite, 4.97–5.14:1 auf Cards). Deaktiviert
  `cursor-not-allowed opacity-50`. Die Box ist 16 px groß, die Klickfläche per `::after`
  40 × 32 px. Beschriftung per `Label htmlFor` oder `aria-label` aus i18n.
- **`Separator`:** `orientation` `horizontal` (Default) oder `vertical`, `decorative`
  Default `true` (`role="none"`, für Screenreader unsichtbar). `decorative={false}` setzt
  `role="separator"` und bei `vertical` `aria-orientation="vertical"`. Linie 1 px in
  `--border`, vertikal per `self-stretch` so hoch wie der Flex-Container.

```tsx
<ContextMenu>
  <ContextMenuTrigger asChild>
    <div tabIndex={0} aria-label={t("canvas.label")} className="rounded-lg border">…</div>
  </ContextMenuTrigger>
  <ContextMenuContent>
    <ContextMenuItem onSelect={copy}>
      {t("common.copy")}
      <ContextMenuShortcut>⌘C</ContextMenuShortcut>
    </ContextMenuItem>
    <ContextMenuCheckboxItem checked={isGridVisible} onCheckedChange={setGridVisible}>
      {t("editor.grid")}
    </ContextMenuCheckboxItem>
    <ContextMenuSeparator />
    <ContextMenuItem variant="destructive" onSelect={remove}>{t("common.delete")}</ContextMenuItem>
  </ContextMenuContent>
</ContextMenu>

<div className="flex items-center gap-2">
  <Checkbox
    id="snap"
    checked={isSnapping}
    onCheckedChange={(state) => setSnapping(state === true)}
  />
  <Label htmlFor="snap">{t("editor.snap")}</Label>
  <Separator orientation="vertical" />
</div>
```

### 10. AlertDialog, Tabs, Switch, Progress, Skeleton, Toast (ab 1.0.0)

shadcn-Form (Stil `radix-nova`) auf den Radix-Primitives aus `radix-ui`; `Toaster` umhüllt
`sonner` (Pflicht-Abhängigkeit des ui-Einstiegs, Abschnitt 1). Keine eingebauten Texte.

- **`AlertDialog`:** `AlertDialog`, `AlertDialogTrigger`, `AlertDialogContent` (`size`
  `default`/`sm`), `AlertDialogHeader`, `AlertDialogMedia`, `AlertDialogTitle`,
  `AlertDialogDescription`, `AlertDialogFooter`, `AlertDialogCancel`, `AlertDialogAction`
  (`variant`/`size` wie `Button`, z. B. `destructive`), `AlertDialogOverlay`,
  `AlertDialogPortal`. `AlertDialogCancel` und `AlertDialogAction` verlangen ihren Text als
  `children`. Kein Schließen per Klick daneben; Escape bricht ab, der Fokus startet auf
  Abbrechen und kehrt zum Auslöser zurück.
- **`Tabs`:** `Tabs` (`orientation`), `TabsList` (`variant` `default`/`line`, per
  `aria-label` benennen), `TabsTrigger`, `TabsContent`, dazu `tabsListVariants`. Aktiver Tab
  bei `default` mit Fläche `--primary-subtle`, Rahmen `--primary` und Innenring, bei `line`
  mit Unterstrich in `--foreground`; also nicht nur über Farbe. Ein Tab-Stopp, Pfeiltasten
  wechseln und aktivieren, deaktivierte Tabs werden übersprungen.
- **`Switch`:** `checked`/`defaultChecked`, `onCheckedChange`, `size` `default`/`sm`. Aus:
  Spur `--input` (≥ 3:1 zu Seite und Card in Hell und Dunkel), Hover Rahmen `--primary`. An:
  Spur `--primary`, Hover `--primary-hover`, gedrückt `--primary-active`. Fokus als Outline
  2 px in `--ring` mit 2 px Abstand. Für Auswahl mit Absenden `Checkbox`.
- **`Progress`:** `value`, `max` (Default 100), Pflicht `aria-label` oder `aria-labelledby`.
  `role="progressbar"` mit `aria-valuemin`/`-max`/`-now`; `value={null}` ist unbestimmt (kein
  `aria-valuenow`). Indikator `--primary`, ohne Bewegung bei reduzierter Bewegung.
- **`Skeleton`:** dekorativ (`aria-hidden`), Fläche `--accent`, pulsiert nicht bei reduzierter
  Bewegung. Den Ladezustand am Container melden (`aria-busy`, sichtbarer Text).
- **`Toaster`, `toast`:** `Toaster` einmal pro App innerhalb von `ThemeProvider`, Pflicht
  `closeLabel` und `regionLabel`; Hell/Dunkel folgt `resolvedTheme`. Meldungen per `toast()`,
  `toast.success()` usw., immer aus `@web-app-kit/frontend/ui` importiert. Region mit
  `aria-live="polite"`, Tabler-Icons, Schließen als Ghost-Icon-Button (24 px), Aktion als
  `Button`, Fokus als Outline in `--ring`.

```tsx
<ThemeProvider>
  <App />
  <Toaster closeLabel={t("common.close")} regionLabel={t("common.notifications")} />
</ThemeProvider>

toast.success(t("items.saved"), { action: { label: t("common.undo"), onClick: undo } });

<div className="flex items-center gap-2">
  <Switch id="autosave" checked={isAutosave} onCheckedChange={setAutosave} />
  <Label htmlFor="autosave">{t("settings.autosave")}</Label>
</div>
```

## App-Shell (`@web-app-kit/frontend/ui`, ab 0.4.0)

Alle Web-Tools bekommen denselben Rahmen: Kopfzeile oben, optional Navigation links und
Detailbereich rechts.

```
┌─────────────────────────────────────┐
│ ◆ Titel   [App-Aktionen]  Sprache ◐ │
├────────┬───────────────────┬────────┤
│ Nav    │ Inhalt            │ Detail │
│ (opt.) │                   │ (opt.) │
└────────┴───────────────────┴────────┘
```

```tsx
import {
  AppShell, LanguageToggle, PageContainer, SidePanel, ThemeToggle,
} from "@web-app-kit/frontend/ui";

<AppShell
  brand={<><Logo className="size-5" />{t("app.title")}</>}
  homeHref="/"
  skipLinkLabel={t("shell.skipToContent")}
  actions={<Button variant="outline" size="sm">{t("project.new")}</Button>}
  controls={
    <>
      <LanguageToggle
        languages={[{ code: "de", label: "Deutsch" }, { code: "en", label: "English" }]}
        value={language}
        onChange={setLanguage}
        describe={({ code, label }) => t("shell.language", { label, code })}
      />
      <ThemeToggle labels={themeLabels} describe={(label) => t("theme.toggleTitle", { label })} />
    </>
  }
  navigation={<SidePanel side="left"><MainNavigation /></SidePanel>}
  navigationLabel={t("shell.navigation")}
  detail={
    <SidePanel
      side="right"
      width="lg"
      title={selected?.name}
      open={selected !== null}
      onOpenChange={() => setSelected(null)}
      closeLabel={t("shell.closeDetail")}
      returnFocusRef={selectedRowRef}
    >
      <ProjectDetail project={selected} />
    </SidePanel>
  }
  detailLabel={t("shell.detail")}
>
  <PageContainer size="lg">{/* Seite */}</PageContainer>
</AppShell>
```

- **`AppShell`:** volle Viewport-Höhe (`h-dvh`), Kopfzeile 3.5rem mit `border-b`. Landmarks
  `<header>`, `<nav aria-label={navigationLabel}>`, `<main>`, `<aside aria-label={detailLabel}>`;
  `nav`/`aside` entstehen nur, wenn `navigation` bzw. `detail` gesetzt ist; der Typ verlangt
  dann `navigationLabel` bzw. `detailLabel`. Solange ein geschlossenes `SidePanel` nichts
  rendert, sind sie per `empty:hidden` unsichtbar. Erster Tab-Stopp ist der Skip-Link
  (`skipLinkLabel`, Hover `overlay-hover`, gedrückt `translate-y-px`, Fokusring) auf
  `<main id="app-content" tabIndex={-1}>` (`APP_SHELL_CONTENT_ID`); nur `<main>` scrollt,
  Kopfzeile und Panels bleiben stehen. Mit `homeHref` wird `brand` ein Link mit
  `INTERACTIVE_SURFACE_CLASSES` (Hover, gedrückt, Fokusring; im Forced-Colors-Modus erzeugt
  Tailwind 4.3.2 für `focus-visible:outline-hidden` einen 2-px-Outline, geprüft im gebauten
  CSS und per `forcedColors: "active"`-Screenshot); ohne `homeHref` reiner Text, beide
  Varianten bündig. Ein zu langer Titel wird abgeschnitten, ebenso `actions`, die nicht in die
  Zeile passen (ohne horizontales Scrollen, geprüft bei 320 px Breite). `actions` stehen links
  nach dem Titel, `controls` rechtsbündig. Unter `md` (48rem) stapeln sich Navigation, Inhalt
  und Detail untereinander; jedes Panel ist dort auf `30dvh` begrenzt und scrollt selbst.
  Apps schließen das Detail-Panel auf schmalen Bildschirmen, wenn es nicht gebraucht wird
  (z. B. erst nach Auswahl öffnen), damit der Inhalt genug Höhe behält.
- **Router-Link für die Marke:** `renderLink` ersetzt das einfache `<a href>` (Default), z. B.
  durch einen Client-Router-Link ohne Seiten-Neuladen. Die Funktion erhält `href`,
  `className` (Hover, gedrückt, Fokusring), `data-slot` und `children`; alles außer `href`
  unverändert weitergeben, sonst fehlen die Zustände:

  ```tsx
  import { Link, Outlet } from "react-router";

  <AppShell
    brand={brand}
    homeHref="/"
    renderLink={({ href, ...props }) => <Link to={href} {...props} />}
    skipLinkLabel={t("shell.skipToContent")}
  >
    <Outlet />
  </AppShell>
  ```
- **`SidePanel`:** gemeinsame Panel-Fläche für beide Seiten mit `--sidebar-*`-Tokens, Rahmen
  zur Inhaltsseite hin. `width`: `sm` 16rem (Default), `md` 20rem, `lg` 24rem. Optionaler
  Kopf aus `title` und `actions`, Inhalt scrollt. Mit `title` rendert der Kopf ein `<h2>` und
  das Panel wird eine benannte Gruppe (`role="group"`, `aria-labelledby` auf die Überschrift);
  ohne `title` gibt es keine Überschrift. `open={false}` rendert nichts; den Öffnen-Auslöser
  baut die App. Mit `onOpenChange` erscheint ein Schließen-Button, der Typ verlangt dann
  `closeLabel`. Nach dem Schließen geht der Fokus auf `returnFocusRef` (z. B. die Zeile oder
  den Button, der das Panel geöffnet hat), ohne Ref auf `<main>`.
- **`PageContainer`:** zentrierter Inhalt für Listen- und Formularseiten, `size` `md`
  (`max-w-3xl`), `lg` (`max-w-7xl`, Default) oder `full`; Abstand `px-4 sm:px-6 py-8`.
- **`LanguageToggle`:** Geschwister des `ThemeToggle` (`ghost`, Höhe 2rem, `IconLanguage` +
  Sprachcode in Großbuchstaben). Klick wechselt zyklisch zur nächsten Sprache in `languages`
  und ruft `onChange(code)`; unbekanntes `value` zeigt die erste Sprache. `describe` erhält
  die aktuelle Option `{ code, label }` und formuliert `aria-label`/`title`. Der sichtbare Code
  muss im zugänglichen Namen stehen (WCAG 2.5.3 „Label in Name"), z. B.
  „Sprache: Deutsch (DE)". Fehlt er im Text von `describe`, stellt die Komponente ihn voran
  („DE Sprache: Deutsch").

### Version im Footer

Jede App zeigt ihren ausgelieferten Stand unten in der Shell, damit sichtbar ist, ob eine
Änderung live ist. Quelle ist `git describe --tags` zum Build-Zeitpunkt: auf einem Release-Tag
`v1.4.0`, danach `v1.4.0-3-gabc1234` (3 Commits nach dem Tag, Commit `abc1234`). Daneben
steht die Kit-Version, mit der die App gebaut ist („Version v1.4.0 · Kit 0.14.0").

```tsx
<AppShell
  brand={t("app.title")}
  skipLinkLabel={t("shell.skipLink")}
  footer={
    <AppVersion
      label={t("shell.version")}
      version={import.meta.env.VITE_APP_VERSION}
      kitLabel={t("shell.kitVersion")}
    />
  }
>
```

- **`footer`** (optional) rendert unter Inhalt und Panels ein `<footer>` (Landmark
  `contentinfo`, `h-8`, `border-t`, `text-xs`, `text-muted-foreground`). Ohne `footer` keine
  Fußzeile.
- **`AppVersion`** zeigt `label` und `version` (`tabular-nums`), Beschriftung aus der App.
  Mit `kitLabel` folgt nach einem Trennpunkt (`aria-hidden`) die Kit-Version: `version` aus
  `package.json` des Frontend-Pakets, zur Build-Zeit von Vite als Konstante eingesetzt. Ohne
  `kitLabel` bleibt die Anzeige wie bisher. App-Version und Kit-Version in `text-foreground`,
  Beschriftungen in `text-muted-foreground`.
- **Backend:** `registerVersionRoute(app, { version: readAppVersion() })` liefert
  `GET /api/version` → `{ "version": "v1.4.0-3-gabc1234", "kitVersion": "0.14.0" }`
  (`cache-control: no-store`, `version` ist `null` ohne Wert). `readAppVersion()` liest
  `APP_VERSION`. `kitVersion` ist `version` aus `package.json` des Backend-Pakets, ohne
  Konfiguration. Pfad per `path` änderbar.
- **TypeScript:** Die Kit-Version kommt per JSON-Import aus `package.json` der Kit-Pakete.
  Die `tsconfig.json` der App braucht dafür `"resolveJsonModule": true` (Frontend und Backend).
- **Build:** Pipeline mit voller Tag-Historie (`clone: depth: full` bzw. `git fetch --tags`),
  dann `docker build --build-arg APP_VERSION="$(git describe --tags --always)" …`. Im
  Dockerfile `ARG APP_VERSION`, für den Frontend-Build `ENV VITE_APP_VERSION=$APP_VERSION`,
  für die Laufzeit `ENV APP_VERSION=$APP_VERSION`.
- **Live-Prüfung:** Footer bzw. `/api/version` gleich `git describe --tags` auf `main`, die
  Kit-Version gleich dem Tag des Submodules ohne `v`
  (`git -C vendor/web-app-kit describe --tags`).

### Primärfarbe

`--primary`, `--primary-foreground`, `--ring` und die `--sidebar-*`-Entsprechungen stehen in
`packages/frontend/src/brand.css` (Hell unter `:root`, Dunkel unter `.dark`); `theme.css`
importiert die Datei. Die Werte gehören dem Team, das den Kit betreibt, und gelten für alle
seine Tools. Ein Test (`src/brand.test.ts`) prüft sie gegen WCAG 2.2 AA:

| Paar | Mindestwert |
| --- | --- |
| `--primary-foreground` auf `--primary` (auch Sidebar) | 4.5:1 |
| `--primary` und `--ring` gegen `--background` / `--sidebar` | 3:1 |

Rollen für Interaktions-Zustände (ebenfalls in `brand.css`, Hell und Dunkel; Utilities
`bg-primary-hover`, `bg-primary-active`, `bg-primary-subtle`, `text-primary-subtle-foreground`,
`text-primary-hover`):

| Rolle | Einsatz |
| --- | --- |
| `--primary-hover` | Hover auf Primärflächen (`Button` `default`, aktives `Toggle`), Link-Hover |
| `--primary-active` | gedrückte Primärfläche, gedrückter Link |
| `--primary-subtle` | zarte Fläche: Hover `ghost`/`outline`/`Toggle` aus, Menü-Fokus, Auswahl |
| `--primary-subtle-foreground` | Text auf `--primary-subtle` |

`src/active-state.test.ts` prüft diese Paare mit den Werten aus `brand.css` und dem Fall
„deep pink sample":

| Paar | Mindestwert (:1) |
| --- | --- |
| `--primary-foreground` auf `--primary`, `--primary-hover`, `--primary-active` | 4.5 |
| `--primary-subtle-foreground`, `--foreground`, `--muted-foreground` auf `--primary-subtle` | 4.5 |
| `--primary-hover` als Text auf `--primary-subtle` | 4.5 |
| `--primary`, `--primary-hover`, `--primary-active` als Text auf `--background`, `--card` | 4.5 |
| `--primary`, `--primary-hover`, `--primary-active` gegen `--background` | 3 |
| `--primary` gegen `--card`, `--popover`, `--primary-subtle` (Rahmen, Ring, Check-Icon) | 3 |
| `--ring` gegen `--background`, `--card`, `--popover`, `--sidebar`, `--primary-subtle` | 3 |

Zustandsmodell: Hover zart (`--primary-subtle`), gedrückt/geöffnet/ausgewählt Primärrahmen
plus Innenring auf `--primary-subtle`, aktives `Toggle`/`ToggleGroupItem` voll (`--primary`,
Hover `--primary-hover`, gedrückt `--primary-active`). Fehlen die vier Rollen in einer
`brand.css`, greifen die neutralen Werte aus `theme.css` (`--primary-hover-default` usw.,
geprüft im selben Test). `--primary-subtle` ist bewusst deckend und
nur zart vom Untergrund abgesetzt (gemessen Hover zu Ruhe 1.08–1.5:1); der Zustand liegt nie
nur in der Farbe, sondern in `aria-pressed`/`aria-expanded`/`aria-current` und Rahmen.

Weicht ein einzelnes Tool ab, setzt es die Variablen per `applyThemeOverrides` und prüft sie
mit `contrastRatio(first, second)` vor dem Einsatz.

Farbwerte in `brand.css` immer als `oklch(L C H)`. Erlaubt sind außerdem `var(…)` und
`color-mix(in oklch, …)`; Hex, `rgb()` und `hsl()` lässt `src/brand.test.ts` für `brand.css`
und `theme.css` scheitern. `contrastRatio` versteht `#rrggbb` nur noch für Messungen.

### Grautöne: Farbstich und Einzelwerte

Alle neutralen Tokens in `theme.css` (Flächen, Text, Rahmen, `--surface-*`, `--sidebar*`,
`--brand*`, die neutralen `--secondary-*`/`--tertiary-*`-Defaults) rechnen sich aus zwei
Seed-Variablen, nach dem Vorbild der getönten shadcn-Basisfarben (Stone, Zinc, Mauve, Olive,
Mist, Taupe):

| Variable | Default | Wirkung |
| --- | --- | --- |
| `--neutral-hue` | `0` | Farbton des Stichs (OKLCH-Hue in Grad) |
| `--neutral-chroma` | `0` | Stärke des Stichs; `0` ergibt exakt die bisherigen Grautöne |

Jeder Grauton behält seine Helligkeit, z. B. `--background: oklch(0.97 var(--neutral-chroma)
var(--neutral-hue))`. `--destructive` und die Primär-Defaults (`--primary-*-default`) bleiben
unverändert. Beispiel in `brand.css`:

```css
:root {
    --neutral-hue: 250;
    --neutral-chroma: 0.02;
}
```

Der Seed unter `:root` gilt für Hell und Dunkel. Zusätzlich lässt sich jeder Grauton einzeln in
`brand.css` setzen, dann immer in `:root` und `.dark` (ein Wert nur unter `:root` gälte sonst
auch im Dunkelmodus):

```css
:root {
    --background: oklch(0.95 0.015 80);
}

.dark {
    --background: oklch(0.17 0.015 80);
}
```

Mechanismus: `theme.css` importiert `brand.css` zuerst und legt die Grautöne danach unter
`:where(:root)` und `:where(.dark)` ab. `:where()` hat die Spezifität 0, ein Wert aus
`brand.css` (`:root`, `.dark`) gewinnt deshalb trotz späterer Position; fehlt er, greift der
Wert aus `theme.css`. Dadurch sind ab dieser Version auch `--secondary` und
`--secondary-foreground` aus `brand.css` wirksam.

Obergrenze: `src/neutral-tint.test.ts` prüft Text auf allen Flächen (4.5:1), `--input` und
`--ring` gegen die Flächen (3:1) für Standard, warm (60/0.012), kühl (250/0.02), Deep Pink
(350/0.02), Einzelwerte sowie `--neutral-chroma: 0.09` für jeden Hue von 0 bis 359. Bis 0.094
bestehen alle Paare, ab 0.095 fällt `--input` (Hell, Hue 17–30) unter 3:1. Für leicht getönte
Grautöne genügen 0.005 bis 0.02: darüber liegen die hellen Flächen (L ≥ 0.97) außerhalb von
sRGB, der Browser schneidet die Farbe ab und das Grau wird sichtbar farbig.

Gemessene Minima (Hell / Dunkel):

| Fall | `--foreground` | `--muted-foreground` | übrige Text-Paare | `--input` | `--ring` |
| --- | --- | --- | --- | --- | --- |
| Standard | 16.11 / 14.43 | 4.88 / 7.02 | 11.50 / 10.02 | 3.34 / 3.52 | 18.15 / 10.17 |
| warm 60/0.012 | 16.09 / 14.39 | 4.89 / 7.02 | 11.49 / 10.00 | 3.34 / 3.52 | 18.11 / 10.19 |
| kühl 250/0.02 | 16.13 / 14.35 | 4.88 / 7.02 | 11.51 / 9.96 | 3.33 / 3.52 | 18.13 / 10.15 |
| Deep Pink 350/0.02 | 16.03 / 14.26 | 4.90 / 7.01 | 11.47 / 9.93 | 3.34 / 3.51 | 17.96 / 10.25 |

„Übrige Text-Paare": `-foreground` auf `--card`, `--popover`, `--secondary` (auch Hover und
gedrückt), `--accent`, `--sidebar*`, `--tertiary` (auch Hover und gedrückt),
`--secondary-subtle`, `--brand`. Ein Wert, den `brand.css` für `--secondary*` oder
`--tertiary*` setzt, hat Vorrang vor dem getönten Default.

`--border` ist eine dekorative Trennlinie (Hell 0.922 auf 0.97) und nicht Teil der Prüfung;
Eingabefelder grenzen sich über `--input` ab.

### Theme ohne Aufblitzen

`ThemeProvider` setzt die `light`/`dark`-Klasse erst nach dem Mounten; bis dahin zeigt die
Seite kurz das helle Theme. `themeInitScript(storageKey, defaultTheme)` liefert dieselbe
Logik als Inline-Skript für `<head>`: gespeicherter Wert, wenn er `light`, `dark` oder
`system` ist, sonst `defaultTheme`; wirft `localStorage` (gesperrter Speicher), gilt ebenfalls
`defaultTheme`; `system` folgt `prefers-color-scheme`. `ThemeProvider` liest und schreibt
seit 0.4.0 genauso (vorher landete ein ungültiger gespeicherter Wert unverändert als Klasse
auf `<html>`); ein Test prüft beide Wege auf identisches Ergebnis. `storageKey` und
`defaultTheme` müssen mit dem `ThemeProvider` übereinstimmen. In `vite.config.ts`:

```ts
import { themeInitScript } from "./node_modules/@web-app-kit/frontend/src/themeInitScript.ts";

plugins: [
  {
    name: "theme-init",
    transformIndexHtml: () => [
      { tag: "script", children: themeInitScript("app-theme", "system"), injectTo: "head-prepend" },
    ],
  },
],
```

Der relative Pfad ist nötig, weil der Kit TypeScript-Quellen ausliefert und Node Module unter
`node_modules` nicht selbst transpiliert; Vite bündelt relativ importierte Dateien der Config.
Wird `vite.config.ts` von `tsc` mitgeprüft (z. B. über `tsconfig.node.json`), meldet `tsc`
für die Endung `.ts` im Importpfad `TS5097`; dort `"allowImportingTsExtensions": true`
setzen (verlangt `noEmit` oder `emitDeclarationOnly`). Alternativ den Rückgabewert einmalig
als `<script>` in `index.html` einfügen.

### Schrift: Noto Sans (lokal)

`theme.css` setzt `--font-sans` auf `"Noto Sans Variable"` mit System-Fallback. Die
Schriftdateien liefert `@fontsource-variable/noto-sans` (optionale Peer-Dependency, keine
Google-/CDN-Abfrage). Die App installiert das Paket und ergänzt eine Zeile vor dem Kit-Theme:

```bash
npm install @fontsource-variable/noto-sans@5.3.0
```

```css
@import "@fontsource-variable/noto-sans";
@import "@web-app-kit/frontend/theme.css";
```

Ohne den Import bleibt die System-Schrift aktiv (`ui-sans-serif, system-ui, sans-serif`).

### Favicon

Vorlage für ein SVG-Favicon je Tool: abgerundete Kachel in `--primary`, darauf ein Kürzel oder
ein Tabler-Icon in `--primary-foreground`. Hell und Dunkel schaltet das SVG selbst per
`@media (prefers-color-scheme: dark)` um (Farbschema des Browsers, nicht der `ThemeToggle`
der Seite). Ein Favicon erbt keine CSS-Variablen der Seite, deshalb setzt
`scripts/create-favicon.mjs` die `oklch`-Werte aus `packages/frontend/src/brand.css` (`:root`
und `.dark`) fest in die Vorlage `scripts/templates/favicon.template.svg` ein. Andere Werte
als `oklch(L C H)` (Hex, `var(…)`, `color-mix`) lehnt das Skript ab.

Im Wurzelordner des Tools (Node `^22.18.0 || >=24.11.0`, keine Abhängigkeiten):

```bash
node vendor/web-app-kit/scripts/create-favicon.mjs --text W
node vendor/web-app-kit/scripts/create-favicon.mjs \
  --icon frontend/node_modules/@tabler/icons/icons/outline/layout-board.svg
```

- `--text`: ein oder zwei Zeichen (Schrift `system-ui`, fett; ein Favicon lädt keine
  Web-Schriften).
- `--icon`: eine Tabler-SVG-Datei (`viewBox="0 0 24 24"`, Outline oder Filled), z. B. aus
  `@tabler/icons`, das `@tabler/icons-react` mitinstalliert.
- `--out` (Default `frontend/public/favicon.svg`), `--brand` (Default `brand.css` des Kits).

`frontend/index.html`:

```html
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
```

Nach einer Änderung der Primärfarbe im Kit das Favicon neu erzeugen und committen.

Kontrast Zeichen zu Kachel = `--primary-foreground` auf `--primary`; `src/brand.test.ts`
verlangt dafür 4.5:1 in Hell und Dunkel, über den 3:1 für Grafiken. Gemessen mit
`contrastRatio`: neutrale Werte 17.16:1 (Hell) / 14.22:1 (Dunkel), Deep Pink
(`oklch(0.5668 0.2294 0.76)` / `oklch(0.695 0.2229 355.31)`) 5.14:1 / 5.88:1.

## ESLint-Plugin (`@web-app-kit/eslint-plugin`)

Prüft beim Linten, dass interaktive Elemente sichtbare Zustände haben (Abschnitt
„Komponenten-Verhalten") und dass ein Tool keine Kit-Bausteine nachbaut. Plain-ESM ohne Build,
ESLint 9 (Flat Config), Pflicht für jede Kit-App.

Regel `web-app-kit/interactive-states`:
- Prüft intrinsische Elemente (kleingeschriebener Tag): `button`, `a` mit `href`, jedes Element
  mit `onClick`/`onKeyDown` sowie `role` `button`, `tab`, `menuitem`, `menuitemcheckbox`,
  `menuitemradio`, `option`, `switch`, `checkbox`, `radio`, `link`. Ausgenommen: `input`,
  `select`, `textarea`, `label`, `option`. Reine Zeiger-Handler (`onPointerDown`,
  `onMouseDown`, z. B. Ziehzonen) lösen die Regel nicht aus.
- Erfüllt, wenn `className` je ein Token mit den Varianten `hover:`, `active:` und
  `focus-visible:` enthält: auch in Ketten wie `not-disabled:hover:…`, als `[&:hover]:…`,
  `[&:active]:…`, `[&:focus-visible]:…` (nicht innerhalb `:not(…)`) oder als
  `group-hover:`/`group-active:`/`group-focus-visible:` an Nachfahren des `group`-Elements
  (`group` muss unbedingt gesetzt sein; benannte Gruppen `group/x` nur mit `group-hover/x:`;
  eine verschachtelte Gruppe gleichen Namens beendet die Suche; am Element selbst zählen
  `group-*`-Varianten nicht; Nachfahren hinter `&&` oder in nur einem `?:`-Zweig sind bedingt).
  Alternativ nutzt `className` einen Klassen-Export aus `kitModules`: Name endet auf
  `_CLASSES` oder `Variants` (`INTERACTIVE_SURFACE_CLASSES`, `STRETCHED_CARD_CLASSES`,
  `STRETCHED_LINK_CLASSES`, `buttonVariants(…)`, `badgeVariants(…)`, auch über
  `import * as Kit`). Andere Kit-Importe (`useTheme()`, `APP_SHELL_CONTENT_ID`) melden
  `unresolved`; `cn` aus dem Kit zählt nicht als Abdeckung, nur seine Argumente.
- Ein Zustand zählt nur, wenn er unbedingt vorkommt oder in allen Zweigen eines `?:`
  (bzw. auf beiden Seiten von `||`/`??`). Klassen hinter `&&`, clsx-Objekt-Keys und die
  Varianten-Klassen einer lokalen `cva`-Fabrik (`variants: { … }`) sind bedingt und erfüllen
  keinen Zustand; die Basis-Klassen der Fabrik zählen.
- Kinder mit `asChild` (ohne Wert oder `{true}`, auch innerhalb `{…}`, `?:`, `&&`) gelten als
  gedeckt, wenn die Eltern-Komponente ihr Kind selbst stylt: `Button`, `Badge` oder `Card` mit
  `interactive` (ohne Wert oder `{true}`; lokale shadcn-Kopien, `Kit.Button` und umbenannte
  Kit-Importe wie `import { Button as KitButton }` eingeschlossen). `Card` ohne `interactive`
  gibt nur Layout-Klassen weiter und deckt nicht ab. Radix-Trigger aus dem Kit
  (`DropdownMenuTrigger`, `TooltipTrigger`, `DialogTrigger` …) geben keine Klassen weiter:
  `<DropdownMenuTrigger asChild><button>` braucht eigene Zustände.
- Ausgewertet werden String- und Template-Literale, `+`, `?:`, `&&`, `||`, `??`, Arrays,
  Objekt-Keys, `cn`/`clsx`/`cva` (auch umbenannt importiert) samt Argumenten, `[…].join()`
  bzw. `[…].join(" ")` (ein anderer Trenner meldet `unresolved`) und `const` im selben Modul,
  darunter Arrow-Funktionen mit Ausdrucks-Body und `cva`-Fabriken. `unresolved` meldet die
  Regel, wenn danach Zustände fehlen und etwas nicht auswertbar war: Import aus einem anderen
  App-Modul, andere Funktionsaufrufe, Parameter, `let`, `props.className`, Zyklen, ein
  Template-Ausdruck mitten in einem Token (`` `hover:${color}` ``), ein nicht auswertbarer
  `className` eines Gruppen-Nachfahren oder ein Spread ohne erfüllten `className`.
- `hover:`, `group-hover:` und `[&:hover]:` mit `bg-muted` oder `bg-accent` (auch mit weiteren
  Varianten, `/50` und `!`) meldet die Regel an jedem Element und in jedem Zweig: auf
  Kit-Flächen unsichtbar, stattdessen `bg-primary-subtle`, `overlay-hover` bzw.
  `bg-surface-hover`.
- Option `kitModules` (Default `["@web-app-kit/frontend", "@web-app-kit/frontend/ui"]`).

Regel `web-app-kit/no-foreign-ui` (ab 1.0.0), verhindert eine zweite UI-Basis neben dem Kit:
- Meldet Importe (auch `export … from`, `export * from` und `import("…")`) aus `radix-ui`,
  `@radix-ui/*`, aus Icon-Paketen außer `@tabler/icons-react` (`lucide`, `lucide-react`,
  `@lucide/*`, `react-icons`, `react-feather`, `@heroicons/*`, `@phosphor-icons/*`,
  `phosphor-react`, `@fortawesome/*`, `@mui/icons-material`, `@iconify/*`, `@mdi/*`,
  `@remixicon/*`, `@ant-design/icons`, `@primer/octicons-react`, `@tabler/icons`) und aus
  `shadcn` außer `shadcn/tailwind.css`.
- Meldet Importe lokaler shadcn-Kopien: Pfade mit dem Segment `components/ui`, die mit `.`,
  `/`, `@/` oder `~/` beginnen (`@/components/ui/tabs`, `../components/ui/skeleton`).
- Meldet jede Datei unter `components/ui/` (auch `src/components/ui/`) einmal am Dateianfang:
  dort liegen kopierte Bausteine. `class-variance-authority`-Nachbauten sind am Import nicht
  erkennbar, der Ordner ist das Merkmal.
- Erlaubt sind eigene App-Elemente aus Kit-Bausteinen und Tokens in jedem anderen Ordner, z. B.
  `src/components/app/`.
- Option `allow` (Default `[]`) für begründete Ausnahmen, etwa eine Chart-Bibliothek: ein
  Eintrag gilt für die exakte Import-Quelle oder einen Pfad darunter (`"recharts"` erlaubt
  `recharts/x`, nicht `recharts-extra`) und ebenso für den Dateipfad relativ zum
  ESLint-Arbeitsordner (`"src/components/ui/chart.tsx"`, Ordner `"src/components/ui/legacy/"`).
  Jede Ausnahme in der `AGENTS.md` des Tools begründen.
- Meldung: Kit-Baustein aus `@web-app-kit/frontend/ui` nutzen; fehlt er, im Kit ergänzen
  (Skill `web-app-kit-component`).

```js
// eslint.config.js mit begründeter Ausnahme
export default [
  ...webAppKit.configs.recommended,
  { rules: { "web-app-kit/no-foreign-ui": ["error", { allow: ["src/components/ui/chart.tsx"] }] } },
];
```

Gilt für Tool-Code, nicht für den Kit: `configs.recommended` nimmt
`**/packages/frontend/src/ui/**` (die Kit-Bausteine, die `radix-ui` legitim importieren) von
dieser Regel aus; in Tools liegt der Kit unter `vendor/` bzw. `node_modules/` und wird ohnehin
nicht gelintet. Gemessen mit `configs.recommended` auf `packages/frontend/src`: 0 Meldungen,
ohne die Ausnahme 19.

`configs.recommended` aktiviert beide Regeln für `**/*.{ts,tsx}` mit `@babel/eslint-parser`
(ohne Babel-Konfigurationsdatei; Babel-Plugins `typescript` für `*.ts`, damit `<T>(x) => x`
und `<number>wert` parsen, und `jsx` + `typescript` für `*.tsx`, jeweils mit `decorators` für
Klassen- und Member-Decorators; Parameter-Decorators parst der Standard nicht) und dazu
`jsx-a11y/no-static-element-interactions`, `click-events-have-key-events`,
`interactive-supports-focus` und `no-noninteractive-element-interactions` als `error`.
Ignoriert `dist`, `node_modules`, `vendor`.

Einbindung in `<app>/frontend`:

```jsonc
// package.json
"scripts": { "lint": "eslint ." },
"devDependencies": {
  "@web-app-kit/eslint-plugin": "file:../vendor/web-app-kit/packages/eslint-plugin",
  "eslint": "9.39.5"
}
```

```js
// eslint.config.js
import webAppKit from "@web-app-kit/eslint-plugin";

export default [...webAppKit.configs.recommended];
```

Docker: Die Dockerfiles der App kopieren vor `npm ci` jedes Kit-Paket, auf das ein Lockfile per
`file:` verweist. Ohne eigene `COPY`-Zeile für das Plugin bricht `npm ci` im Image mit `ENOENT`
auf `…/packages/eslint-plugin/package.json` ab, lokal wie im Pipeline-Step für das Image:

```dockerfile
# frontend/Dockerfile.dev (Ziel /vendor/…) und Dockerfile (Ziel /app/vendor/…)
COPY vendor/web-app-kit/packages/frontend /app/vendor/web-app-kit/packages/frontend
COPY vendor/web-app-kit/packages/eslint-plugin /app/vendor/web-app-kit/packages/eslint-plugin
```

Danach `docker build -f Dockerfile .` und den Dev-Build (`docker compose build frontend`)
lokal prüfen, nicht nur `npm install` auf dem Host.

Der Babel-Parser parst TypeScript ohne das Paket `typescript` und funktioniert daher auch in
Apps mit TypeScript 7. Voraussetzung ist Node `^22.18.0 || >=24.11.0` (`engines` des Plugins,
Vorgabe von `@babel/eslint-parser` 8).

Bestehende Verstöße einfrieren statt sofort beheben: einmal `npx eslint . --suppress-all`
ausführen und die erzeugte `eslint-suppressions.json` committen. Neue Verstöße schlagen
danach fehl. Behobene Stellen aus der Datei entfernen mit
`npx eslint . --prune-suppressions`.

## Agent-Skills

Der Kit liefert vier agent-neutrale Skills (Format Agent Skills, `SKILL.md` + `references/`)
unter `.agents/skills/`. Damit baut ein KI-Agent ein Tool ohne Vorwissen nach dem Kit-Muster:

| Skill | Zweck | Commands |
| --- | --- | --- |
| `web-app-kit-new-tool` | neues Tool von Null bis zum laufenden Container, Vorlage für die `AGENTS.md` des Tools, Deploy-Anforderungen | `how-to`, `scaffold`, `checklist`, `deploy` |
| `web-app-kit-ui` | UI nur aus Kit-Bausteinen, Zustände, WCAG 2.2 AA, Theme | `how-to`, `check`, `theme` |
| `web-app-kit-update` | Kit-Pin im Tool anheben | `how-to`, `check`, `apply` |
| `web-app-kit-component` | neuen Baustein im Kit-Repository ergänzen: shadcn-Vorlage, Kit-Rollen, Zustände, DOM-Test, Doku, Release | `how-to`, `add`, `check` |

Ein Tool bekommt die Skills per Skript, ausgeführt im Wurzelordner des Tools:

```bash
bash vendor/web-app-kit/scripts/link-agent-skills.sh          # Links anlegen/aktualisieren
bash vendor/web-app-kit/scripts/link-agent-skills.sh --check  # nur prüfen, Exit 1 bei Abweichung
```

Das Skript legt je Kit-Skill relative Links `.agents/skills/<skill>` und
`.claude/skills/<skill>` auf `../../vendor/web-app-kit/.agents/skills/<skill>` an. Es ist
idempotent, entfernt Links auf Skills, die der Kit nicht mehr hat, und bricht ohne Änderung
ab (Exit 3), wenn ein gleichnamiger echter Ordner, eine Datei oder ein fremder Link im Weg
liegt. Die Links werden im Tool committet; nach jedem Kit-Update das Skript erneut ausführen.
Die `AGENTS.md` des Tools verweist auf die Skills (Vorlage:
`.agents/skills/web-app-kit-new-tool/references/agents-template.md`).

`.agents/skills/web-app-kit-new-tool/references/deploy.md` enthält die allgemeinen
Deploy-Anforderungen und eine Eintragsstelle für das Deploy-Ziel, die jedes Team in seinem
Kit-Repository selbst pflegt.

## Katalog

`apps/catalog` zeigt alle Bausteine aus `@web-app-kit/frontend/ui` mit Beschreibung, Import,
Varianten, Zuständen und Live-Vorschau in Hell und Dunkel, dazu Farbrollen samt Grautönen
(Kontrast gerendert gemessen), Radien und eine Statusübersicht. Rahmen und Navigation sind
neutral; die Vorschauen laufen in eigenen iframes und zeigen die Farben aus `brand.css`.

```bash
npm install
npm run catalog:dev     # http://localhost:5199
npm run catalog:build   # Ausgabe in apps/catalog/dist
```

Pflicht-Eintrag je Baustein: Jeder Export aus `packages/frontend/src/ui/index.ts` braucht einen
Eintrag in `apps/catalog/src/catalog/components/<name>.tsx` (Name, Gruppe, Status,
Beschreibung, Exporte, Varianten mit Vorschau-Komponente, Zustände), aufgenommen in
`apps/catalog/src/catalog/entries.ts`. Hilfs-Exporte ohne eigene Oberfläche (`cn`,
`*Variants`, `*_CLASSES`, `APP_SHELL_CONTENT_ID`) stehen mit Begründung in
`apps/catalog/src/catalog/export-exceptions.ts`. `npm test` schlägt fehl, solange ein Export
weder Eintrag noch Ausnahme hat, und ebenso bei Einträgen für Exporte, die es nicht mehr gibt.
Vorschauen nutzen nur Kit-Bausteine und Tabler-Icons; Texte im Katalog sind Deutsch.

## CI

`bitbucket-pipelines.yml` prüft das Kit-Repository auf jedem Pull Request und auf `main`
(Image `node:24`, kein Deploy):

| Schritt | Prüft |
| --- | --- |
| `npm install` | Abhängigkeiten aller Workspaces (`package-lock.json` ist nicht eingecheckt) |
| `npm run typecheck` | Typen aller Pakete und des Katalogs |
| `npm test` | Tests aller Pakete, Katalog-Pflichteinträge |
| `npm run test:scripts` | Scripts unter `scripts/` (`tag-release.sh`, `check-kit-version.sh`, …) |
| `npm run catalog:build` | Katalog baut |
| `npm run lint -w @web-app-kit/catalog` | Katalog gegen `@web-app-kit/eslint-plugin` |

Lint-Gate in den Tools (verbindlich): Jedes Tool bindet `webAppKit.configs.recommended` in
`frontend/eslint.config.js` ein (Abschnitt „ESLint-Plugin“), und der Frontend-Schritt der
Tool-Pipeline ruft `npm run lint` ohne `--if-present` auf. Ein Treffer von
`web-app-kit/interactive-states`, `web-app-kit/no-foreign-ui` oder den `jsx-a11y`-Regeln
bricht den Build ab. Fehlt dem Frontend das Skript `lint`, schlägt der Schritt ebenfalls fehl.

## Konventionen

Kanonische Implementierung der Muster, die taste-web als erster Konsument bereits nutzt
(`AppError`, `ThemeProvider`, Fetch-Client). Jede App bleibt bei ihrem eigenen
domänenspezifischen Code (Models, Routes, Components) — der Kit deckt nur das
Wiederkehrende ab.

### Tailwind v4 Preflight: Buttons ohne `cursor: pointer`

Tailwind v4 setzt `button { cursor: default }` (anders als v3, das implizit `pointer`
gab). Ohne Gegenmaßnahme fehlt jedem `<button>` das Hover-/Klick-Feedback des Zeigers,
während `<a>`-Links es automatisch behalten. Trat unabhängig in zwei Repo-Clustern auf
(hersteller-io-website, dann workbench/taste/mockup/layerboard). Jede neue App auf
Basis von `web-app-skeleton` hat die Regel bereits in `frontend/src/index.css`
(`@layer base`), bei einem Frontend ohne Skeleton-Ursprung selbst ergänzen:

```css
button:not(:disabled) {
  cursor: pointer;
}
```
