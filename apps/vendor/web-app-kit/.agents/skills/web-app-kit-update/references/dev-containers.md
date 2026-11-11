# Lokale Dev-Container nach dem Update

Docker befüllt ein benanntes Volume nur beim ersten Anlegen aus dem Image. Die
`node_modules`-Volumes der Dev-Container enthalten deshalb nach dem Update weiter die alte
Kit-Kopie, auch nach `docker compose build`.

## Prüfen

```bash
docker compose run --rm backend npm ls @web-app-kit/backend
docker compose run --rm frontend npm ls @web-app-kit/frontend @web-app-kit/eslint-plugin
```

Zeigen beide die Version aus `cat vendor/web-app-kit/VERSION`: fertig.

## Alte Kopie im Volume

Volume-Namen ermitteln, nicht raten (Docker stellt den Compose-Projektnamen voran):

```bash
docker volume ls --filter "label=com.docker.compose.project=<projektname>"
```

`<projektname>` steht als `name:` in `docker-compose.yml`, sonst ist es der Ordnername.

Neu anlegen erst nach Rückfrage beim Nutzer, und nur die beiden `node_modules`-Volumes dieses
Projekts: Container stoppen (`docker compose down`, ohne `-v`), die zwei Volumes mit
`docker volume rm` entfernen, `docker compose up --build`.

- Nie `docker compose down -v`, nie Datenbank-Volumes, kein `docker volume prune`.
- Danach die Prüfung oben wiederholen. Vite im Container bündelt beim Start neu, weil der
  Lockfile über den Bind-Mount geändert ist.
