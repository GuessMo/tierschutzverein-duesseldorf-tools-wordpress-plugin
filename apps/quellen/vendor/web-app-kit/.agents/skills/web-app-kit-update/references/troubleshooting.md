# Fehlerbilder und Rückweg

| Symptom | Ursache | Lösung |
| --- | --- | --- |
| `npm ls` zeigt alte Version | Lockfile nicht erneuert | Schritt 4 vollständig, mit `rm -rf` und `npm install <paket>@file:…` |
| `npm ci` im Image: `ENOENT …/packages/<paket>/package.json` | `COPY`-Zeile für ein Kit-Paket fehlt | Zeile in `Dockerfile` und `Dockerfile.dev` ergänzen |
| Kit-Klassen fehlen nur im Production-Build | `@source` in `src/index.css` fehlt | `@source "../node_modules/@web-app-kit/frontend/src";` |
| `Invalid hook call` | Kit als Symlink statt Kopie eingebunden | `install-links=true` in `.npmrc`, Schritt 4 wiederholen |
| Dev-Server zeigt alte Komponenten, Build ist richtig | Vite-Cache `node_modules/.vite` | Dev-Server neu starten, bei gleicher Kit-Version `npx vite --force` |
| Dev-Container zeigt alte Komponenten | `node_modules`-Volume veraltet | `dev-containers.md` |
| Kein Abschnitt „Prüfbefehle" in der Ausgabe | Skript aus dem alten Pin, oder Einträge ohne `Prüfen:` | Submodule auf den Ziel-Tag (Schritt 2), Skript erneut ausführen |
| `fetch --tags` im Submodule: „would clobber existing tag" | lokaler Tag weicht vom Remote ab | `git -C vendor/web-app-kit fetch --force --tags origin`, dann erneut |
| Exit 5 bei `check` | Pin zeigt auf unveröffentlichten Tag | nicht weiterarbeiten, Team fragen |
| Push blockiert: `pre-push: blocked by kit pin check` | Pin ist kein veröffentlichter Tag oder lokale Tags weichen ab | Ausgabe des Hooks lesen, auf veröffentlichten Tag pinnen bzw. Tags mit `--force` holen |
| Push blockiert: `kit pin check not found` | `APPLE_APP_KIT_HOME` zeigt nicht auf einen apple-app-kit-Checkout | Nutzer fragen: Variable setzen oder Checkout anlegen |

## Rückweg bei Abbruch

Update abbrechen, bevor committet ist:

```bash
git -C vendor/web-app-kit checkout "$(git ls-tree HEAD vendor/web-app-kit | awk '{print $3}')"
git restore backend/package.json backend/package-lock.json \
  frontend/package.json frontend/package-lock.json
```

Danach Schritt 4 mit dem alten Pin wiederholen, damit `node_modules` wieder die alte Kit-Kopie
enthält. In Schritt 5 geänderte Dateien einzeln mit `git restore <datei>` zurücksetzen; fremde
Änderungen nicht anfassen.
