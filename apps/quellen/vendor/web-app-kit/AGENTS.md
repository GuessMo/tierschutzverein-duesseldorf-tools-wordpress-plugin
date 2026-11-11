# web-app-kit — Agent-Regeln

Agent-neutral (Claude, OpenCode, Codex). Geteilter Kern der internen Fastify/Vite-Web-Tools
(splitter, mockup, layerboard, taste, workbench): Fehlerklassen, Env-Helfer, ALB-Identität,
Theme, API-Client, gemeinsame UI-Komponenten. Apps binden den Kit als Git-Submodule
`vendor/web-app-kit` auf ein Release-Tag ein.

Required skills: coding-standards, git, ux-best-practices, wcag-aa, shadcn, ui-design, impeccable

## Konventionen

- Jede Änderung, die Apps betrifft, bekommt ein neues Tag (`vX.Y.Z`); Apps heben den
  Submodule-Zeiger bewusst an. Breaking Changes nur mit Major-Sprung.
- UI: Icons ausschließlich Tabler (`@tabler/icons-react`). Theme-Umschalter:
  ein Icon-Button, Zyklus System → Hell → Dunkel.
- Keine fest eingebauten Texte für Nutzer: Beschriftungen kommen per Props aus der App (i18n).
- Kit-Apps binden `@web-app-kit/eslint-plugin` (`configs.recommended`) verpflichtend ein
  (README „ESLint-Plugin").
- WCAG 2.2 AA als Untergrenze für Tokens und Komponenten, Nachweis in Hell und Dunkel.
- Ausnahme zu coding-standards Regel 10: shadcn-Komponenten behalten ihre Prop-Typen inline
  (Upstream-Form, damit shadcn-Updates abgleichbar bleiben). Eigene Kit-Typen liegen in
  `*.types.ts`.
- Ausnahme zu coding-standards Regel 16: Upstream-shadcn-Klassenstrings bleiben inhaltlich
  wie geliefert, auch mit px in Arbeitswerten (`rounded-[min(var(--radius-md),10px)]`,
  `min-w-[96px]`, `ring-[3px]`). Nur Zeilenumbruch und Auslagerung in Konstanten, kein
  Umschreiben. Eigene Kit-Klassen folgen Regel 16.

## Ein UI-System je App (verbindlich)

Kit-Apps nutzen ausschließlich den Kit (shadcn-Basis, Kit-Theme), keine zweite
Komponenten-Bibliothek daneben. Fehlt einem Kit-Tool ein Baustein, kommt er in den Kit.

## Skills

Agent-Skills für Tools auf Kit-Basis liegen unter `.agents/skills/` (README „Agent-Skills"):

- `web-app-kit-new-tool` — neues Tool anlegen, Checkliste, Deploy-Anforderungen
- `web-app-kit-ui` — UI mit Kit-Bausteinen, Zustände, WCAG 2.2 AA, Theme
- `web-app-kit-update` — Kit-Pin in einem Tool anheben

Tools verlinken sie mit `scripts/link-agent-skills.sh` (Test: `npm run test:scripts`). Ändert
sich eine Kit-API, die Skills im selben PR nachziehen.

## Versionierung (verbindlich)

Eine Kit-Version für alle Pakete: `VERSION` = `version` in `packages/backend/package.json` und
`packages/frontend/package.json`. Historie in `CHANGELOG.md` (neueste oben), jede Fassung ein
annotiertes Git-Tag `vX.Y.Z` auf `origin/main`.
- **PATCH** = Bugfix, App-Aktion keine. **MINOR** = neue/geänderte Funktion, abwärtskompatibel,
  App-Aktion optional. **MAJOR** = Breaking, App-Aktion Pflicht mit Umbau-Liste im Eintrag.
- Änderungen ohne Paket-Code (Tooling, Scripts, Doku) = PATCH, App-Aktion keine.
- Jeder CHANGELOG-Eintrag (`## [X.Y.Z] — JJJJ-MM-TT`) hat eine Zeile
  `App-Aktion: keine | optional | Pflicht …`.

Release: Feature-Branch → `VERSION`, beide `package.json` und CHANGELOG-Eintrag im selben PR →
Squash-Merge auf `main` → auf aktuellem `main` (HEAD == `origin/main`, sauberer Baum)
`scripts/tag-release.sh` (prüft Version, Pakete, CHANGELOG, Tag-Freiheit; legt das Tag an und
nennt den Push-Befehl, `--push` pusht selbst). Nie Commits taggen, die nicht auf `origin/main`
liegen; ein vergebenes Tag wird nie verschoben, sondern die Version angehoben.

App-Update: im App-Repo `bash vendor/web-app-kit/scripts/check-kit-version.sh` (Exit 1 =
veraltet, zeigt die CHANGELOG-Einträge ab dem Pin, `!!` markiert Pflicht). Das Script holt die
Kit-Tags per `git fetch --tags` ins Submodule; Arbeitsbaum und Pin bleiben unverändert. Nachziehen:
Submodule auf das neue Tag auschecken, Einträge abarbeiten, Kopie und Lockfile erneuern
(README „Nutzung"), bauen, Submodule-Zeiger committen. Pin-Integrität vor jedem Push prüft der
`pre-push`-Hook aus apple-app-kit (`scripts/check-kit-pin.sh`, erkennt `web-app-kit`;
Installation `scripts/install-pin-check-hook.sh`), hier nicht dupliziert.

Worktrees: jeder Branch in einem eigenen Worktree (`git worktree add ../web-app-kit-<thema>
-b <branch> origin/main`). Im geteilten Checkout `web-app-kit` nie den Branch wechseln.

Script-Tests: `npm run test:scripts`.
