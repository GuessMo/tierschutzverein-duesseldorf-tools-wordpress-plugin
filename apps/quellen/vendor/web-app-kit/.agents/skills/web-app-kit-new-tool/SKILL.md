---
name: web-app-kit-new-tool
description: Legt ein neues internes Web-Tool auf Basis von web-app-kit an, von leerem Ordner bis zum laufenden Container - Repo-Layout backend/ + frontend/, Kit als Git-Submodule vendor/web-app-kit auf Release-Tag, Fastify-Backend mit Kit-Helfern (Fehlerklassen, Static Site, Version-Route, Shutdown), Vite/React/Tailwind-v4-Frontend mit App-Rahmen, Theme, i18n und ESLint-Plugin, Dockerfile, docker-compose, AGENTS.md und verlinkte Kit-Skills. Verwenden bei "neues Tool/neue Web-App mit dem Kit bauen", "Tool aufsetzen/scaffolden", "Projekt auf web-app-kit-Basis starten", "Dockerfile/Compose für ein Kit-Tool", "ist mein Tool vollständig eingerichtet" oder "wohin deployen". Nicht für Seiten und Komponenten in einem bestehenden Tool (web-app-kit-ui) und nicht für das Anheben der Kit-Version (web-app-kit-update).
license: MIT
metadata:
  version: "1.0.0"
  abgleich: "2026-11-04 skills.sh+agent-skill.co+aiskill.market+agentskills.io"
---

# web-app-kit: neues Tool anlegen

Baut ein internes Web-Tool nach dem Muster aller Kit-Tools: ein Repo, darin `backend/`
(Fastify) und `frontend/` (Vite + React), der Kit als Git-Submodule unter
`vendor/web-app-kit`, ein Container-Image, das beides ausliefert.

## Commands

| Command | Wirkung |
| --- | --- |
| `how-to` | Kurzanleitung für Leser ohne Vorwissen ausgeben |
| `scaffold <tool-name>` | Neues Tool Schritt für Schritt anlegen (Abschnitt „Scaffold") |
| `checklist` | Bestehendes Tool gegen die Pflichtpunkte prüfen (Abschnitt „Checkliste") |
| `deploy` | Deploy-Anforderungen und Team-Ziel aus `references/deploy.md` zeigen |

Bei leerem oder unbekanntem Argument diese Liste ausgeben und stoppen.

## How-To

Bei Aufruf mit `how-to` diese Ansicht ausgeben.

- **Zweck:** Ein neues internes Web-Tool so anlegen, dass es wie alle anderen Tools aussieht,
  sich gleich bedienen lässt und mit einem Kit-Update automatisch Verbesserungen bekommt.
- **Wann:** Du willst ein neues Werkzeug für das Team bauen (Liste, Formular, Dashboard,
  Editor) oder prüfen, ob ein Tool korrekt eingerichtet ist.
- **Setup:** Installiert sein müssen `git`, Node `>=24.11` (oder `^22.18`), `npm` und Docker.
  Du brauchst Lesezugriff auf das web-app-kit-Repository (URL im Team erfragen).
- **Beispiel:** `scaffold urlaubsplaner` → Agent fragt Anzeigename, Zweck, Ports, Datenbank ja
  oder nein, legt alle Dateien an, baut das Image und zeigt die laufende App unter
  `http://localhost:<Port>`.
- **Ergebnis/Verifikation:** `npm run lint` und `npm run build` im Frontend, `npm test` im
  Backend grün; `docker build` erfolgreich; `GET /api/version` liefert die Version, die Seite
  zeigt Kopfzeile mit Sprach- und Theme-Umschalter und die Version im Footer.

## Grundregeln (gelten in jedem Schritt)

- Genau ein UI-System: nur `@web-app-kit/frontend/ui`, Icons nur `@tabler/icons-react`. Keine
  lokalen Kopien von Kit-Komponenten, keine zweite Komponenten-Bibliothek.
- Keine fest eingebauten Texte für Nutzer, auch nicht in `aria-label`/`title`: alles über i18n.
- `@web-app-kit/eslint-plugin` ist Pflicht und läuft ohne Fehler.
- Den Kit im Submodule nie ändern. Fehlt etwas, Änderung im Kit-Repository vorschlagen.
- Versionen der Vorlagen exakt übernehmen, ohne `^`. Anheben nur gemeinsam per Kit-Release, nie
  im einzelnen Tool. `npm audit`-Meldungen nicht mit `npm audit fix` lösen, sondern dem Nutzer
  melden: das nächste Kit-Release hebt die Pins an.
- Code: Englisch, kleine Dateien, keine Kommentare, Typen in `*.types.ts`/`types.ts`.
- Unklare Angaben (Name, Ports, Datenbank) beim Nutzer erfragen, nie raten.
- Im Tool sind die Skill-Ordner Symlinks auf `vendor/web-app-kit/.agents/skills/`. Referenzen
  per Pfad lesen (`cat .agents/skills/web-app-kit-new-tool/references/backend.md`); suchen mit
  `find -L` bzw. `grep -R`, weil `find` und `grep -r` Links sonst nicht folgen.

## Scaffold

Platzhalter: `<tool>` = Ordner- und Paketname (kebab-case), `<Tool-Name>` = Anzeigename,
`<BACKEND_PORT>`/`<FRONTEND_PORT>` = freie lokale Ports.

### 1. Eckdaten klären

Beim Nutzer erfragen und notieren:

| Angabe | Prüfung |
| --- | --- |
| `<tool>` | kebab-case, nicht vergeben |
| `<Tool-Name>`, Zweck in einem Satz | für Titel und `AGENTS.md` |
| `<BACKEND_PORT>`, `<FRONTEND_PORT>` | frei: `lsof -nP -iTCP:<port> -sTCP:LISTEN` gibt nichts aus |
| Datenbank nötig? | nur wenn Daten dauerhaft gespeichert werden |
| Login-Proxy mit Identitäts-Header? | entscheidet über `createAlbIdentityResolver` |
| Kit-Repository-URL | im Team erfragen |

### 2. Repository anlegen

```bash
mkdir <tool> && cd <tool>
git init -b main
```

`.gitignore` und `.env.example` aus `references/docker.md` anlegen.

### 3. Kit als Submodule auf Release-Tag

```bash
git submodule add <kit-repo-url> vendor/web-app-kit
git -C vendor/web-app-kit fetch --tags
git -C vendor/web-app-kit tag -l 'v*' --sort=-v:refname | head -1
git -C vendor/web-app-kit checkout <neuester-tag>
```

Immer ein Tag `vX.Y.Z` pinnen, nie einen Branch. Prüfen:
`git -C vendor/web-app-kit describe --tags --exact-match` gibt den Tag aus.

### 4. Kit-Skills im Tool verlinken

```bash
bash vendor/web-app-kit/scripts/link-agent-skills.sh
```

Legt relative Links `.agents/skills/<skill>` und `.claude/skills/<skill>` auf
`vendor/web-app-kit/.agents/skills/<skill>` an. Bei Konflikt (gleichnamiger echter Ordner)
bricht das Skript ohne Änderung ab: Ordner prüfen, nicht löschen ohne Rückfrage. Die Links
werden committet. Fehlt das Skript, ist der Pin zu alt: neueren Tag wählen.

### 5. Backend

Alle Dateien nach `references/backend.md`: `.npmrc`, `package.json`, `tsconfig.json`,
`src/config/env.ts`, `src/index.ts`, `src/api/apiRoutes.ts`, Test. Danach:

```bash
cd backend && npm install && npm run typecheck && npm test && cd ..
```

Eine npm-Warnung zu Install-Skripten (z. B. `esbuild`, `fsevents`) ist harmlos: nichts
freischalten, nichts per Flag unterdrücken.

### 6. Frontend

Alle Dateien nach `references/frontend.md`: `.npmrc`, `package.json`, `tsconfig.json`,
`vite.config.ts`, `eslint.config.js`, `index.html`, `src/index.css`, `src/vite-env.d.ts`,
`src/main.tsx`, i18n, App-Rahmen (`ToolLayout.tsx`), `App.tsx`, `src/api/client.ts`. Danach:

```bash
cd frontend && npm install && npm run lint && npm run build && cd ..
```

Seiten und Komponenten danach mit Skill `web-app-kit-ui` bauen.

### 7. Container

`Dockerfile`, `backend/Dockerfile.dev`, `frontend/Dockerfile.dev`, `docker-compose.yml`,
`.dockerignore` nach `references/docker.md`. Mit Datenbank zusätzlich den Abschnitt „Mit
Datenbank". Lokal starten: `docker compose up --build`. Das Produktions-Image erst nach dem
ersten Commit bauen (Schritt 10).

### 8. `AGENTS.md`, `CLAUDE.md` und `README.md`

- `AGENTS.md` aus `references/agents-template.md`, Platzhalter ersetzen.
- `CLAUDE.md` mit der einzigen Zeile `@AGENTS.md`.
- `README.md`: Zweck, lokaler Start (`docker compose up --build`), Ports, Prüfbefehle. Keine
  Deploy-Details wiederholen, auf `references/deploy.md` verweisen.

### 9. Erster Commit

Vor dem Image-Bau: `git describe` braucht einen Commit. Backend und Frontend sind nach Schritt 5
und 6 grün.

```bash
git add .gitmodules vendor/web-app-kit .agents .claude backend frontend Dockerfile \
  docker-compose.yml .dockerignore .gitignore .env.example AGENTS.md CLAUDE.md README.md
git status --short
git commit -m "feat: scaffold <tool> on web-app-kit <tag>"
```

`git status --short` vorher lesen: keine `.env`, keine `node_modules`, kein `dist`. Pushen
und Remote-Repository anlegen nur nach Rückfrage.

### 10. Prüfen

Abschnitt „Checkliste" vollständig abarbeiten, inklusive Image-Bau. Korrekturen als weiterer
Commit. Erst bei allen Punkten grün fertig melden.

## Checkliste

Für `checklist` jeden Punkt mit dem genannten Befehl prüfen und das Ergebnis als Tabelle
(Punkt, Befehl, ok/fehlt) ausgeben. Nichts als erfüllt melden, was nicht gemessen wurde.
Befehle im Repo-Wurzelordner des Tools ausführen.

1. Kit auf Release-Tag: `git -C vendor/web-app-kit describe --tags --exact-match` gibt `vX.Y.Z`.
2. Kit aktuell: `bash vendor/web-app-kit/scripts/check-kit-version.sh` endet mit Exit 0.
3. Skill-Links: `bash vendor/web-app-kit/scripts/link-agent-skills.sh --check` endet mit Exit 0.
4. `install-links=true`: `cat backend/.npmrc frontend/.npmrc`.
5. Kit-Pakete per `file:../vendor/web-app-kit/packages/...`:
   `grep -n web-app-kit backend/package.json frontend/package.json`.
6. Backend-Helfer: `grep -n register backend/src/index.ts` zeigt `registerErrorHandler`,
   `registerVersionRoute`, `registerStaticSite` und `registerShutdown`.
7. Backend grün: `(cd backend && npm run typecheck && npm test)`.
8. ESLint-Plugin: `frontend/eslint.config.js` enthält `webAppKit.configs.recommended`.
9. Frontend grün: `(cd frontend && npm run lint && npm run build)`.
10. CSS-Kopf: `frontend/src/index.css` importiert Noto Sans und `theme.css` und enthält
    `@source "../node_modules/@web-app-kit/frontend/src"`.
11. Theme ohne Aufblitzen: `themeInitScript` in `frontend/vite.config.ts` mit demselben
    `storageKey` und `defaultTheme` wie `ThemeProvider` in `main.tsx`.
12. Rahmen: `AppShell` mit `skipLinkLabel`, `ThemeToggle`, `LanguageToggle` und
    `footer={<AppVersion …/>}`: `grep -rn "AppVersion" frontend/src`.
13. Keine lokalen UI-Kopien: `test ! -d frontend/src/components/ui && echo ok`.
14. Keine festen Texte: `grep -rnE 'aria-label="|title="|placeholder="' frontend/src` findet
    keine Literale; JSX-Texte stichprobenartig lesen, alle über `t("…")`.
15. Fehleranzeige: `grep -rnE "error\.message|UserFacingError\(t\(" frontend/src` findet nichts;
    der Fehler-State hält das Fehlerobjekt oder einen i18n-Schlüssel, übersetzt wird beim
    Rendern über `localizeError(error, t)` (`references/frontend.md`).
16. Kit-Pakete im Dockerfile: zu jeder `file:`-Abhängigkeit eine Zeile
    `COPY vendor/web-app-kit/packages/<paket>` in `Dockerfile` und im passenden `Dockerfile.dev`.
17. Image baut (nach dem ersten Commit). Vor dem ersten Release-Tag ist die Version der
    Commit-Hash, ohne Commit `dev`:

    ```bash
    docker build \
      --build-arg APP_VERSION="$(git describe --tags --always 2>/dev/null || echo dev)" \
      -t <tool>:local .
    ```

18. Image läuft: `docker run`, dann `curl /api/version` (Version) und `curl /` (200), Befehle in
    `references/docker.md`.
19. `AGENTS.md` vorhanden und verweist auf die drei Kit-Skills; `CLAUDE.md` enthält `@AGENTS.md`.
20. UI-Prüfung: Skill `web-app-kit-ui`, Command `check`.

## Deploy

Command `deploy`: `references/deploy.md` lesen und ausgeben. Steht dort im Abschnitt
„Deploy-Ziel dieses Teams" nur „hier eintragen", keine Deploy-Schritte erfinden: dem Nutzer
die allgemeinen Anforderungen nennen und das Team nach Registry, Pipeline, Laufzeit und Zugang
fragen.

## Referenzen

| Datei | Inhalt |
| --- | --- |
| `references/backend.md` | Backend-Dateien, Reihenfolge der Kit-Helfer, Fehlerklassen, Identität, Datenbank, Test |
| `references/frontend.md` | Frontend-Dateien, CSS, Theme, i18n, App-Rahmen, API-Client |
| `references/docker.md` | Dockerfile, Dev-Images, Compose, Ignore-Dateien, Datenbank-Variante |
| `references/agents-template.md` | Vorlage für `AGENTS.md` des Tools |
| `references/deploy.md` | Deploy-Anforderungen, vom Team gepflegtes Deploy-Ziel |

*Zuletzt aktualisiert: 2026-11-04*
