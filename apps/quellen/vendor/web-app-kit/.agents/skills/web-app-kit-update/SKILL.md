---
name: web-app-kit-update
description: Hebt den web-app-kit-Pin in einem Tool an - prüft mit check-kit-version.sh, ob der Kit veraltet ist, arbeitet die CHANGELOG-Einträge ab dem Pin ab (!! = Pflicht), setzt das Submodule vendor/web-app-kit auf das neue Release-Tag, erneuert Paket-Kopie und Lockfile, verlinkt die Kit-Skills neu, baut, testet und committet den Submodule-Zeiger. Verwenden bei "Kit aktualisieren/updaten/anheben", "neue web-app-kit-Version übernehmen", "ist der Kit aktuell", "check-kit-version meldet VERALTET", "Submodule auf neuen Tag", "npm ls zeigt alte Kit-Version". Nicht für das Anlegen eines neuen Tools (web-app-kit-new-tool), nicht für UI-Arbeit (web-app-kit-ui) und nicht für Releases des Kits selbst.
license: MIT
metadata:
  version: "1.0.0"
  abgleich: "2026-11-04 skills.sh+agent-skill.co+aiskill.market+agentskills.io"
---

# web-app-kit: Kit-Version im Tool anheben

Ein Tool ist gegen genau einen Kit-Tag gebaut (Submodule `vendor/web-app-kit`). Neue
Kit-Versionen bringen Fixes und Funktionen; manche verlangen Umbauten im Tool. Dieser Skill
hebt den Pin kontrolliert an.

## Commands

| Command | Wirkung |
| --- | --- |
| `how-to` | Kurzanleitung für Leser ohne Vorwissen ausgeben |
| `check` | Prüfen, ob der Kit veraltet ist, und die offenen CHANGELOG-Einträge zeigen |
| `apply [vX.Y.Z]` | Pin auf den neuesten (oder angegebenen) Tag anheben, Einträge abarbeiten, prüfen, committen |

Bei leerem oder unbekanntem Argument diese Liste ausgeben und stoppen.

## How-To

Bei Aufruf mit `how-to` diese Ansicht ausgeben.

- **Zweck:** Ein Tool auf die neueste web-app-kit-Version bringen, ohne etwas zu übersehen,
  was die neue Version vom Tool verlangt.
- **Wann:** Regelmäßig, wenn `check` „VERALTET" meldet, oder wenn du eine neue Kit-Funktion
  brauchst.
- **Setup:** Im Wurzelordner des Tools, Arbeitsbaum sauber (`git status` leer), Zugriff auf
  das Kit-Repository, Node und npm, für den Container-Test Docker.
- **Beispiel:** `check` meldet `v0.7.0 -> v0.8.0` mit „App-Aktion: optional"; `apply` setzt
  das Submodule auf `v0.8.0`, erneuert die Kit-Kopie, baut die Versionsanzeige ein, baut und
  testet, committet den neuen Pin.
- **Ergebnis/Verifikation:** `check` meldet danach „Aktuell." (Exit 0), `npm ls` zeigt die neue
  Kit-Version, Lint, Build, Tests und Docker-Build sind grün, alle Pflicht-Einträge erledigt.

## Check

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
```

Das Skript holt die Kit-Tags (`git fetch --tags` im Submodule), ändert aber weder Pin noch
Arbeitsbaum.

| Exit | Bedeutung | Nächster Schritt |
| --- | --- | --- |
| 0 | „Aktuell." | nichts zu tun |
| 1 | „VERALTET." mit CHANGELOG-Einträgen ab dem Pin, oder Pin ist kein Release-Tag | `apply` |
| 2 | kein Kit-Submodule | `git submodule update --init` |
| 3 | keine Kit-Tags | Remote-Zugriff prüfen, Team fragen |
| 4 | kein Git-Repo | im Wurzelordner des Tools ausführen |
| 5 | Pin neuer als Remote | Tag ist nicht veröffentlicht: Team fragen, nichts ändern |
| 64 | falscher Aufruf | ohne oder mit genau einem Pfad-Argument aufrufen |

In der Ausgabe markiert `!!` jede Zeile `App-Aktion: Pflicht`. Am Ende steht dann
„Mindestens ein Eintrag verlangt App-Aktion: Pflicht".

Ältere Pins ohne Skript im Submodule: das Skript aus einem aktuellen Kit-Checkout mit dem
Pfad des Tools aufrufen, `bash <kit-checkout>/scripts/check-kit-version.sh <tool-pfad>`.

## Apply

### 1. Ausgangslage sichern

```bash
git status --short
git switch -c chore/web-app-kit-<neuer-tag>
```

Arbeitsbaum muss sauber sein. Kein Update auf einem geschützten Hauptzweig direkt.

### 2. Einträge lesen und Aufgabenliste anlegen

`check` ausführen und jeden CHANGELOG-Eintrag zwischen Pin und Ziel-Tag lesen. Pro Eintrag
eine Aufgabe:

| `App-Aktion` | Umgang |
| --- | --- |
| `Pflicht` (`!!`) | jeder Punkt der Umbau-Liste ist Pflicht, in diesem Update erledigen |
| `optional` | Nutzen kurz nennen, Nutzer entscheiden lassen; Ablehnung im Commit vermerken |
| `keine` | nichts zu tun, nur lesen |

Über mehrere Major-Sprünge: Einträge in Versionsreihenfolge abarbeiten, nichts überspringen.

### 3. Submodule auf den neuen Tag

```bash
git -C vendor/web-app-kit fetch --tags
git -C vendor/web-app-kit checkout <neuer-tag>
git -C vendor/web-app-kit describe --tags --exact-match
```

Immer ein Tag, nie ein Branch oder einzelner Commit.

### 4. Kit-Kopie und Lockfile erneuern

Mit `install-links=true` liegt eine Kopie des Kits in `node_modules`. Ein einfaches
`npm install` aktualisiert den Lockfile-Eintrag nicht zuverlässig, deshalb je Paket:

```bash
cd backend
rm -rf node_modules/@web-app-kit
npm install @web-app-kit/backend@file:../vendor/web-app-kit/packages/backend
npm ls @web-app-kit/backend
cd ../frontend
rm -rf node_modules/@web-app-kit
npm install @web-app-kit/frontend@file:../vendor/web-app-kit/packages/frontend
npm install --save-dev @web-app-kit/eslint-plugin@file:../vendor/web-app-kit/packages/eslint-plugin
npm ls @web-app-kit/frontend @web-app-kit/eslint-plugin
cd ..
```

- `npm ls` muss die Version des neuen Tags zeigen (`cat vendor/web-app-kit/VERSION`).
- `git diff backend/package.json frontend/package.json`: die `file:`-Angaben bleiben gleich.
- Neue Peer-Dependencies aus dem CHANGELOG mit exakter Version installieren.

### 5. CHANGELOG-Aufgaben umsetzen

Jede Aufgabe aus Schritt 2 umsetzen. Bei UI-Änderungen Skill `web-app-kit-ui` befolgen, bei
Änderungen an Dockerfile, Backend-Start oder Frontend-Einrichtung die Vorlagen aus Skill
`web-app-kit-new-tool` (`references/`). Ein neues Kit-Paket mit `file:`-Abhängigkeit braucht
eine eigene `COPY vendor/web-app-kit/packages/<paket>`-Zeile in `Dockerfile` und im passenden
`Dockerfile.dev`, sonst bricht `npm ci` im Image mit `ENOENT` ab.

### 6. Skills neu verlinken

```bash
bash vendor/web-app-kit/scripts/link-agent-skills.sh
```

Legt Links zu neuen Kit-Skills an und entfernt Links auf Skills, die der Kit nicht mehr hat.
Konflikt (gleichnamiger echter Ordner): das Skript ändert nichts; Ordner prüfen und den Nutzer
fragen. Gibt es das Skript im neuen Tag nicht, Schritt überspringen.

### 7. Prüfen

```bash
(cd backend && npm run typecheck && npm test)
(cd frontend && npm run lint && npm run build)
docker build --build-arg APP_VERSION="$(git describe --tags --always 2>/dev/null || echo dev)" \
  -t <tool>:update .
docker compose build
```

- Neue Lint-Fehler durch eine strengere Regel im Kit beheben, nicht abschalten. Nur wenn der
  CHANGELOG ausdrücklich das Einfrieren erlaubt: `npx eslint . --suppress-all` und die Datei
  committen.
- Lokale Dev-Container: Docker befüllt ein benanntes Volume nur beim ersten Anlegen. Die
  `node_modules`-Volumes enthalten daher weiter die alte Kit-Kopie. Prüfen mit
  `docker compose run --rm frontend npm ls @web-app-kit/frontend`. Zeigt es die alte Version:
  `docker compose down`, nur die beiden `node_modules`-Volumes löschen
  (`docker volume ls`, dann `docker volume rm <projekt>_<tool>_backend_node_modules
  <projekt>_<tool>_frontend_node_modules`), `docker compose up --build`. Datenbank-Volumes nie
  löschen, kein `down -v`.
- UI im Browser kurz in Hell und Dunkel ansehen; bei UI-Änderungen im CHANGELOG die Prüfliste
  aus Skill `web-app-kit-ui` (`check`).

### 8. Committen

```bash
git add vendor/web-app-kit backend/package-lock.json frontend/package-lock.json .agents .claude
git add <weitere geänderte Dateien>
git status --short
git commit -m "chore(deps): bump web-app-kit to <neuer-tag>"
```

Im Commit-Text die erledigten Pflicht-Punkte und abgelehnte optionale Punkte nennen. Push und
Pull Request nur nach Rückfrage, nach dem Git-Ablauf des Teams.

### 9. Abschluss

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
bash vendor/web-app-kit/scripts/link-agent-skills.sh --check
```

Beide mit Exit 0. Erst dann das Update als fertig melden, mit: alter und neuer Tag, erledigte
Pflicht-Punkte, Entscheidung je optionalem Punkt, Ergebnisse aus Schritt 7.

## Fehlerbilder

| Symptom | Ursache | Lösung |
| --- | --- | --- |
| `npm ls` zeigt alte Version | Lockfile nicht erneuert | Schritt 4 vollständig, mit `rm -rf` und `npm install <paket>@file:…` |
| `npm ci` im Image: `ENOENT …/packages/<paket>/package.json` | `COPY`-Zeile für ein Kit-Paket fehlt | Zeile in `Dockerfile` und `Dockerfile.dev` ergänzen |
| Kit-Klassen fehlen nur im Production-Build | `@source` in `src/index.css` fehlt | `@source "../node_modules/@web-app-kit/frontend/src";` |
| `Invalid hook call` | Kit als Symlink statt Kopie eingebunden | `install-links=true` in `.npmrc`, Schritt 4 wiederholen |
| Dev-Container zeigt alte Komponenten | `node_modules`-Volume veraltet | Schritt 7, Volumes der `node_modules` neu anlegen |
| Exit 5 bei `check` | Pin zeigt auf unveröffentlichten Tag | nicht weiterarbeiten, Team fragen |

*Zuletzt aktualisiert: 2026-11-04*
