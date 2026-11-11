---
name: web-app-kit-update
description: Hebt den web-app-kit-Pin in einem Tool an - prüft mit check-kit-version.sh, ob der Kit veraltet ist, arbeitet die CHANGELOG-Einträge ab dem Pin ab (!! = Pflicht), setzt das Submodule vendor/web-app-kit auf das neue Release-Tag, erneuert Paket-Kopie und Lockfile, verlinkt die Kit-Skills neu, baut, testet und committet den Submodule-Zeiger. Verwenden bei "Kit aktualisieren/updaten/anheben", "neue web-app-kit-Version übernehmen", "ist der Kit aktuell", "check-kit-version meldet VERALTET", "Submodule auf neuen Tag", "npm ls zeigt alte Kit-Version". Nicht für das Anlegen eines neuen Tools (web-app-kit-new-tool), nicht für UI-Arbeit (web-app-kit-ui) und nicht für Releases des Kits selbst.
license: MIT
metadata:
  version: "1.1.0"
  abgleich: "2026-11-04 skills.sh+agent-skill.co+aiskill.market+agentskills.io"
---

# web-app-kit: Kit-Version im Tool anheben

Ein Tool ist gegen genau einen Kit-Tag gebaut (Submodule `vendor/web-app-kit`). Jeder
CHANGELOG-Eintrag ab 1.0.0 sagt in `### Neu`, was es gibt, und in `### Umbau`, was das Tool
ändern muss, oft mit einer Zeile `Prüfen: \`<befehl>\``, die betroffene Stellen findet. Dieser
Skill hebt den Pin an und arbeitet die Umbau-Punkte einzeln ab.

## Commands

| Command | Wirkung |
| --- | --- |
| `how-to` | Kurzanleitung für Leser ohne Vorwissen ausgeben |
| `check` | Prüfen, ob der Kit veraltet ist, und die offenen CHANGELOG-Einträge zeigen |
| `apply [vX.Y.Z]` | Pin auf den neuesten (oder angegebenen) Tag anheben, Umbau-Punkte abarbeiten, prüfen, committen |

Bei leerem oder unbekanntem Argument diese Liste ausgeben und stoppen.

## How-To

Bei Aufruf mit `how-to` diese Ansicht ausgeben.

- **Zweck:** Ein Tool auf die neueste web-app-kit-Version bringen, ohne einen Umbau zu
  übersehen, den die neue Version vom Tool verlangt.
- **Wann:** Wenn `check` „VERALTET" meldet, oder wenn du eine neue Kit-Funktion brauchst.
- **Setup:** Im Wurzelordner des Tools, Arbeitsbaum sauber (`git status` leer), Zugriff auf
  das Kit-Repository, Node und npm, für den Container-Test Docker.
- **Beispiel:** `check` meldet `v1.1.0 -> v1.2.0`; der Eintrag 1.2.0 nennt unter `### Umbau`
  „Lokale `components/ui/switch.tsx` löschen" mit
  `Prüfen: \`grep -rl "components/ui/switch" frontend/src\``. `apply` setzt das Submodule auf
  `v1.2.0`, das Skript meldet „betroffen: frontend/src/settings.tsx", der Punkt wird umgesetzt,
  bis „nicht betroffen" erscheint, dann Lockfile, Build, Tests, Commit.
- **Ergebnis/Verifikation:** Jeder Umbau-Punkt mit Prüfbefehl meldet „nicht betroffen",
  `check` meldet nach dem Commit „Aktuell." (Exit 0), `npm ls` zeigt die neue Kit-Version,
  Lint, Build, Tests und Docker-Build sind grün.

## Check

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
```

Holt die Kit-Tags (`git fetch --tags` im Submodule), ändert weder Pin noch Arbeitsbaum. Pin ist
der im letzten Commit gespeicherte Submodule-Stand (`git ls-tree HEAD`).

| Exit | Bedeutung | Nächster Schritt |
| --- | --- | --- |
| 0 | „Aktuell." | nichts zu tun |
| 1 | „VERALTET." mit Einträgen ab dem Pin, oder Pin ist kein Release-Tag | `apply` |
| 2 | kein Kit-Submodule | `git submodule update --init` |
| 3 | keine Kit-Tags | Remote-Zugriff prüfen, Team fragen |
| 4 | kein Git-Repo | im Wurzelordner des Tools ausführen |
| 5 | Pin neuer als Remote | Tag ist nicht veröffentlicht: Team fragen, nichts ändern |
| 64 | falscher Aufruf | ohne oder mit genau einem Pfad-Argument aufrufen |

Ausgabe: Einträge neueste oben, `!!` vor jeder Zeile `App-Aktion: Pflicht`, danach je
`Prüfen:`-Befehl „betroffen: <dateien>", „nicht betroffen", „Prüfung fehlgeschlagen: …" oder
„nicht ausgeführt" (automatisch laufen nur `grep`, `rg`, `find`, `ls`, `test`). Das Skript
stammt aus dem ausgecheckten Submodule; ein alter Pin kennt Prüfbefehle und neue Blöcke noch
nicht, deshalb in `apply` nach Schritt 2 erneut ausführen. Pin ohne Skript (vor v0.3.2):
`bash <kit-checkout>/scripts/check-kit-version.sh <tool-pfad>`.

## Apply

### 1. Ausgangslage sichern

```bash
git status --short
git fetch origin
git switch -c chore/web-app-kit-<neuer-tag> origin/<hauptzweig>
```

Arbeitsbaum sauber, Branch vom aktuellen Remote-Stand, nie direkt auf dem Hauptzweig.

### 2. Submodule auf den Ziel-Tag

```bash
git -C vendor/web-app-kit fetch --tags
git -C vendor/web-app-kit checkout <neuer-tag>
git -C vendor/web-app-kit describe --tags --exact-match
```

Immer ein Tag, nie ein Branch oder einzelner Commit. Rückweg bei Abbruch:
`references/troubleshooting.md`.

### 3. Umbau-Liste aufstellen

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
```

Jetzt läuft das Skript des Ziel-Tags gegen den alten Pin: alle Einträge zwischen Pin und
neuestem Tag mit Prüfergebnissen. Daraus eine Aufgabenliste, **eine Aufgabe je Umbau-Punkt**
(nicht je Eintrag):

- Reihenfolge: älteste Version zuerst (Ausgabe von unten nach oben lesen), über Major-Sprünge
  nichts überspringen.
- `apply` mit einem Tag, der nicht der neueste ist: Einträge oberhalb des Ziel-Tags ignorieren.
- Einträge vor 1.0.0 haben keinen Block `### Umbau`: Aufgaben aus dem Text und der Zeile
  `App-Aktion` ableiten.

| `App-Aktion` | Umgang mit den Umbau-Punkten |
| --- | --- |
| `Pflicht` (`!!`) | jeder Punkt ist Pflicht, in diesem Update erledigen |
| `optional` | Nutzen kurz nennen, Nutzer entscheiden lassen; Ablehnung im Commit vermerken |
| `keine` | `### Umbau` enthält „keiner": nur `### Neu` lesen |

Je Aufgabe festhalten: Version, Schritt, Prüfbefehl, Ergebnis vorher. „nicht betroffen" schon
vorher: Punkt ist geprüft und erledigt. „nicht ausgeführt" oder „Prüfung fehlgeschlagen": den
Befehl lesen und nur ausführen, wenn er nichts ändert; sonst die Stellen von Hand suchen.

### 4. Kit-Kopie und Lockfile erneuern

Mit `install-links=true` liegt eine Kopie des Kits in `node_modules`. Ein einfaches
`npm install` aktualisiert den Lockfile-Eintrag nicht zuverlässig (Kit-README „Nutzung"):

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

- `npm ls` zeigt die Version aus `cat vendor/web-app-kit/VERSION`.
- `git diff backend/package.json frontend/package.json`: die `file:`-Angaben bleiben gleich;
  neue Peer-Dependencies aus `### Umbau` mit exakter Version installieren.
- Laufender Vite-Dev-Server: neu starten. Vite bündelt nach geändertem Lockfile neu
  (Cache `node_modules/.vite`); bei unveränderter Kit-Version `npx vite --force`.

### 5. Umbau-Punkte einzeln abarbeiten

Je Aufgabe aus Schritt 3, in der festgelegten Reihenfolge:

1. Prüfbefehl ausführen, Treffer notieren (vorher).
2. Schritt umsetzen. UI: Skill `web-app-kit-ui`; Dockerfile, Backend-Start,
   Frontend-Einrichtung: Vorlagen im Skill `web-app-kit-new-tool` (`references/`).
3. Prüfbefehl erneut ausführen (nachher). Erst ohne Treffer abhaken. Bleiben Treffer, die so
   richtig sind, den Grund an der Aufgabe festhalten.

Danach das Skript erneut ausführen: jeder Prüfbefehl meldet „nicht betroffen" (Exit bleibt 1,
der neue Pin ist noch nicht committet). Ein neues Kit-Paket mit `file:`-Abhängigkeit braucht
eine `COPY vendor/web-app-kit/packages/<paket>`-Zeile in `Dockerfile` und `Dockerfile.dev`.

### 6. Skills neu verlinken

```bash
bash vendor/web-app-kit/scripts/link-agent-skills.sh
```

Legt Links zu neuen Kit-Skills an, entfernt Links auf entfernte. Exit 3 (echter Ordner im
Weg): nichts ändern, Nutzer fragen. Fehlt das Skript im Ziel-Tag (vor v0.9.0): überspringen.

### 7. Prüfen

```bash
(cd backend && npm run typecheck && npm test)
(cd frontend && npm run lint && npm run build)
docker build --build-arg APP_VERSION="$(git describe --tags --always 2>/dev/null || echo dev)" \
  -t <tool>:update .
docker compose build
```

- Neue Lint-Fehler beheben, nicht abschalten. `npx eslint . --suppress-all` nur, wenn
  `### Umbau` das Einfrieren ausdrücklich erlaubt.
- Lokale Dev-Container behalten die alte Kit-Kopie im `node_modules`-Volume:
  `references/dev-containers.md` (prüfen, Volumes nur nach Rückfrage neu anlegen).
- UI kurz in Hell und Dunkel ansehen; bei UI-Umbauten die Prüfliste aus Skill `web-app-kit-ui`.

### 8. Committen

```bash
git add vendor/web-app-kit backend/package.json backend/package-lock.json \
  frontend/package.json frontend/package-lock.json .agents/skills .claude/skills
git add <in Schritt 5 geänderte Dateien>
git status --short
git commit -m "chore(deps): bump web-app-kit to <neuer-tag>"
```

Commit-Text: alter und neuer Tag, je Umbau-Punkt erledigt oder abgelehnt. Andere Checkouts
brauchen nach dem Pull `git submodule update --init` und `npm ci` in `backend/` und
`frontend/`; das gehört in die PR-Beschreibung.

### 9. Pin-Hook und Abschluss

```bash
bash vendor/web-app-kit/scripts/check-kit-version.sh
bash vendor/web-app-kit/scripts/link-agent-skills.sh --check
ls "$(git rev-parse --git-path hooks)/pre-push"
```

- Beide Skripte mit Exit 0.
- Der `pre-push`-Hook prüft den Pin vor jedem Push (Pin ist veröffentlichter Tag, lokale Tags
  gleich Remote). Fehlt er, Nutzer auf die Installation hinweisen:
  `bash "${APPLE_APP_KIT_HOME:-$HOME/Apps/apple-app-kit}/scripts/install-pin-check-hook.sh"`.
  Der Hook sucht das Prüfskript unter `APPLE_APP_KIT_HOME`; fehlt es dort, blockt er jeden Push.
- Push und Pull Request nur nach Rückfrage, nach dem Git-Ablauf des Teams.

Fertig melden mit: alter und neuer Tag, Umbau-Punkte mit Prüfergebnis vorher und nachher,
Entscheidung je optionalem Punkt, Ergebnisse aus Schritt 7.

## Fehlerbilder

Symptom, Ursache, Lösung und Rückweg bei Abbruch: `references/troubleshooting.md`.

*Zuletzt aktualisiert: 2026-11-05 — Historie: references/changelog.md*
