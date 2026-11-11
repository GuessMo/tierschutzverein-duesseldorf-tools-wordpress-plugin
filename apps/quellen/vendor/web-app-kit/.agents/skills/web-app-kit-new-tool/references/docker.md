# Container: Dockerfile, Dev-Images, docker-compose

Ein Image pro Tool: das Backend liefert das gebaute Frontend selbst aus (`STATIC_DIR`).
Lokal laufen Backend und Frontend als zwei Dev-Container mit Hot-Reload. Platzhalter:
`<tool>`, `<BACKEND_PORT>`, `<FRONTEND_PORT>`.

## `Dockerfile` (Produktion, im Repo-Wurzelordner)

```dockerfile
FROM node:24-alpine AS frontend-build
WORKDIR /app/frontend
COPY vendor/web-app-kit/packages/frontend /app/vendor/web-app-kit/packages/frontend
COPY vendor/web-app-kit/packages/eslint-plugin /app/vendor/web-app-kit/packages/eslint-plugin
COPY frontend/package.json frontend/package-lock.json frontend/.npmrc ./
RUN npm ci
COPY frontend/ ./
ARG APP_VERSION=""
ENV VITE_APP_VERSION=$APP_VERSION
RUN npm run build

FROM node:24-alpine AS backend-build
WORKDIR /app/backend
COPY vendor/web-app-kit/packages/backend /app/vendor/web-app-kit/packages/backend
COPY backend/package.json backend/package-lock.json backend/.npmrc ./
RUN npm ci
COPY backend/ ./
RUN npm run build && npm prune --omit=dev

FROM node:24-alpine AS runtime
ARG APP_VERSION=""
ENV NODE_ENV=production \
    PORT=<BACKEND_PORT> \
    STATIC_DIR=/app/public \
    APP_VERSION=$APP_VERSION
WORKDIR /app/backend
COPY --from=backend-build /app/backend/package.json ./
COPY --from=backend-build /app/backend/node_modules ./node_modules
COPY --from=backend-build /app/backend/src ./src
COPY --from=frontend-build /app/frontend/dist /app/public
USER node
EXPOSE <BACKEND_PORT>
CMD ["node", "--import", "tsx", "src/index.ts"]
```

Regeln:

| Regel | Grund |
| --- | --- |
| Jedes Kit-Paket, auf das ein Lockfile per `file:` zeigt, vor `npm ci` kopieren | sonst `ENOENT` auf `…/packages/<paket>/package.json` |
| Ziel `/app/vendor/web-app-kit/...` bei `WORKDIR /app/<teil>` | `file:../vendor/...` löst genau dorthin auf |
| `ARG APP_VERSION` in jeder Stufe, die ihn nutzt | `ARG` gilt nur innerhalb seiner Stufe |
| `ARG`/`ENV` erst nach `npm ci` | eine neue Version macht den Install-Cache nicht ungültig |
| `VITE_APP_VERSION` im Frontend-Build, `APP_VERSION` zur Laufzeit | Footer (`AppVersion`) und `GET /api/version` zeigen denselben Stand |
| `USER node` | Container läuft nicht als root |

Bauen und prüfen, erst nach dem ersten Commit (`git describe` braucht einen Commit, für Tags
die volle Historie):

```bash
docker build \
  --build-arg APP_VERSION="$(git describe --tags --always 2>/dev/null || echo dev)" \
  -t <tool>:local .
docker run --rm -p <BACKEND_PORT>:<BACKEND_PORT> <tool>:local
curl -s http://localhost:<BACKEND_PORT>/api/version
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:<BACKEND_PORT>/
```

Erwartet: `{"version":"<git describe>"}` und `200`. Vor dem ersten Release-Tag ist die
Version der kurze Commit-Hash (z. B. `a1b2c3d`), ohne Commit `dev`. Ein unbekannter SPA-Pfad (z. B.
`/irgendwas` mit `Accept: text/html`) liefert ebenfalls `index.html`, ein unbekannter
`/api/...`-Pfad ein JSON-404.

## `backend/Dockerfile.dev`

```dockerfile
FROM node:24-alpine

COPY vendor/web-app-kit/packages/backend /vendor/web-app-kit/packages/backend

WORKDIR /app

COPY backend/package.json backend/package-lock.json backend/.npmrc ./
RUN npm ci

COPY backend/ ./

EXPOSE <BACKEND_PORT>

CMD ["npm", "run", "dev"]
```

## `frontend/Dockerfile.dev`

```dockerfile
FROM node:24-alpine

COPY vendor/web-app-kit/packages/frontend /vendor/web-app-kit/packages/frontend
COPY vendor/web-app-kit/packages/eslint-plugin /vendor/web-app-kit/packages/eslint-plugin

WORKDIR /app

COPY frontend/package.json frontend/package-lock.json frontend/.npmrc ./
RUN npm ci

COPY frontend/ ./

EXPOSE <FRONTEND_PORT>

CMD ["npm", "run", "dev"]
```

Hier ist `WORKDIR /app`, deshalb liegt der Kit unter `/vendor/...` (`file:../vendor/...`).

## `docker-compose.yml` (lokal)

```yaml
name: <tool>

services:
  backend:
    build:
      context: .
      dockerfile: backend/Dockerfile.dev
    environment:
      PORT: ${BACKEND_PORT:-<BACKEND_PORT>}
      CORS_ORIGIN: ${CORS_ORIGIN:-http://localhost:<FRONTEND_PORT>}
      CHOKIDAR_USEPOLLING: "true"
    ports:
      - "${BACKEND_PORT:-<BACKEND_PORT>}:${BACKEND_PORT:-<BACKEND_PORT>}"
    volumes:
      - ./backend:/app
      - <tool>_backend_node_modules:/app/node_modules
      - ./vendor/web-app-kit:/vendor/web-app-kit:ro

  frontend:
    build:
      context: .
      dockerfile: frontend/Dockerfile.dev
    environment:
      VITE_BACKEND_URL: http://localhost:${BACKEND_PORT:-<BACKEND_PORT>}
      CHOKIDAR_USEPOLLING: "true"
    ports:
      - "${FRONTEND_PORT:-<FRONTEND_PORT>}:<FRONTEND_PORT>"
    volumes:
      - ./frontend:/app
      - <tool>_frontend_node_modules:/app/node_modules
      - ./vendor/web-app-kit:/vendor/web-app-kit:ro
    depends_on:
      - backend

volumes:
  <tool>_backend_node_modules:
  <tool>_frontend_node_modules:
```

Start: `docker compose up --build`. Frontend unter `http://localhost:<FRONTEND_PORT>`,
Backend unter `http://localhost:<BACKEND_PORT>/api/version`. Benannte Volumes enthalten die
`node_modules` der Container und den Vite-Cache (`node_modules/.vite`). Docker befüllt ein
Volume nur beim ersten Anlegen aus dem Image: nach Änderungen an `package.json` oder einem
Kit-Update genügt `docker compose build` nicht, die Volumes behalten die alte Kopie. Prüfen und
Volumes gezielt neu anlegen: Skill `web-app-kit-update`, `references/dev-containers.md`.
Volumes nur bewusst löschen (`down -v` entfernt auch Datenbank-Daten).

Ohne Docker geht es auch: in `backend/` und `frontend/` je `npm run dev`.

## `.env.example`

```ini
BACKEND_PORT=<BACKEND_PORT>
FRONTEND_PORT=<FRONTEND_PORT>
CORS_ORIGIN=http://localhost:<FRONTEND_PORT>
```

`.env` selbst nie committen.

## `.dockerignore`

```
**/node_modules
**/dist
**/.git
**/*.log
**/*.tsbuildinfo
**/*.test.ts
**/.DS_Store
**/.env
**/.env.*
!**/.env.example
Dockerfile
docker-compose.yml
docker-compose.*.yml
```

## `.gitignore`

```
node_modules/
dist/
.env
.env.*
!.env.example
*.log
.DS_Store
*.tsbuildinfo
```

## Mit Datenbank (optional)

Nur wenn das Tool Daten speichert (siehe `backend.md`, Abschnitt Datenbank):

- `docker-compose.yml`: Dienst `postgres` (`postgres:16-alpine`, eigener Host-Port, benanntes
  Volume, `healthcheck` mit `pg_isready`), im Backend `DATABASE_URL` und
  `depends_on: { postgres: { condition: service_healthy } }`.
- `Dockerfile`, Stufe `backend-build`: vor `npm run build`
  `DATABASE_URL=postgresql://build:build@localhost:5432/build prisma generate`, dazu
  `ENV PATH=/app/backend/node_modules/.bin:$PATH`.
- Stufe `runtime`: zusätzlich `prisma/` und `prisma.config.ts` kopieren, gleiche `PATH`-Zeile,
  Start mit `CMD ["sh", "-c", "prisma migrate deploy && exec node --import tsx src/index.ts"]`.
