# Backend (Fastify + Kit)

Ordner `backend/`. Platzhalter: `<tool>` (kebab-case), `<BACKEND_PORT>`, `<FRONTEND_PORT>`.
Versionen der Vorlage exakt übernehmen (ohne `^`). Anheben nur gemeinsam per Kit-Release, nicht
im einzelnen Tool. `npm audit`-Meldungen nicht mit `npm audit fix` lösen, sondern melden.

## Dateien

```
backend/
├── .npmrc
├── package.json
├── tsconfig.json
└── src/
    ├── index.ts           Server-Start
    ├── config/env.ts      Umgebungsvariablen
    ├── api/apiRoutes.ts   Routen unter /api
    └── api/*.test.ts      Tests (node:test)
```

## `.npmrc`

```ini
install-links=true
```

Pflicht. npm kopiert den Kit dann nach `node_modules/@web-app-kit/*` statt zu verlinken
(sonst doppelte Abhängigkeiten, im Docker-Build fehlende Pfade).

## `package.json`

```json
{
  "name": "<tool>-backend",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "tsx watch src/index.ts",
    "build": "tsc -p tsconfig.json",
    "start": "node --import tsx src/index.ts",
    "typecheck": "tsc -p tsconfig.json --noEmit",
    "test": "node --import tsx --test \"src/**/*.test.ts\""
  },
  "dependencies": {
    "@fastify/cors": "11.3.0",
    "@web-app-kit/backend": "file:../vendor/web-app-kit/packages/backend",
    "fastify": "5.10.0",
    "tsx": "4.23.1"
  },
  "devDependencies": {
    "@types/node": "26.1.1",
    "typescript": "7.0.2"
  }
}
```

Installieren im Ordner `backend/`: `npm install` (erzeugt `package-lock.json`, committen).
Eine npm-Warnung zu Install-Skripten (z. B. `esbuild`, `fsevents`) ist harmlos.
`tsx` steht unter `dependencies`, weil der Container `src/index.ts` direkt mit `tsx` startet.

## `tsconfig.json`

```json
{
  "compilerOptions": {
    "target": "ES2023",
    "module": "NodeNext",
    "moduleResolution": "NodeNext",
    "outDir": "dist",
    "rootDir": "src",
    "strict": true,
    "esModuleInterop": true,
    "skipLibCheck": true,
    "resolveJsonModule": true
  },
  "include": ["src"]
}
```

Relative Importe brauchen die Endung `.js` (`./config/env.js`), Pflicht bei `NodeNext`.

## `src/config/env.ts`

```ts
import path from "node:path";
import { loadLocalEnv, parseCorsOrigins } from "@web-app-kit/backend";

loadLocalEnv();

export const port = Number(process.env.PORT ?? <BACKEND_PORT>);
export const corsOrigin = parseCorsOrigins(process.env.CORS_ORIGIN, [
  "http://localhost:<FRONTEND_PORT>",
]);
export const staticDir = process.env.STATIC_DIR ? path.resolve(process.env.STATIC_DIR) : undefined;
```

- `loadLocalEnv()` liest `.env`, falls vorhanden; im Container kommen die Werte direkt aus der
  Umgebung.
- Pflichtwerte ohne sinnvollen Default mit `requireEnv("NAME")` lesen: wirft beim Start mit
  klarer Meldung statt später.
- `STATIC_DIR` setzt nur das Container-Image (`/app/public`). Lokal bleibt es leer, dann liefert
  Vite das Frontend aus.

## `src/index.ts`

```ts
import Fastify from "fastify";
import cors from "@fastify/cors";
import {
  readAppVersion,
  registerErrorHandler,
  registerShutdown,
  registerStaticSite,
  registerVersionRoute,
} from "@web-app-kit/backend";
import { corsOrigin, port, staticDir } from "./config/env.js";
import { apiRoutes } from "./api/apiRoutes.js";

const app = Fastify({ logger: true });

registerErrorHandler(app);

await app.register(cors, {
  origin: corsOrigin,
  methods: ["GET", "POST", "PUT", "PATCH", "DELETE"],
});

registerVersionRoute(app, { version: readAppVersion() });

await app.register(apiRoutes, { prefix: "/api" });

if (staticDir) await registerStaticSite(app, { staticDir });

registerShutdown(app);

app.listen({ port, host: "0.0.0.0" }).catch((error) => {
  app.log.error(error);
  process.exit(1);
});
```

Reihenfolge einhalten:

| Schritt | Warum |
| --- | --- |
| `registerErrorHandler` zuerst | Alle Fehler kommen als `{ "error": "<Meldung>" }` mit echtem Statuscode |
| `registerVersionRoute` | `GET /api/version` → `{ "version": "v1.4.0-3-gabc1234", "kitVersion": "0.14.0" }`, dient auch als Health-Check |
| eigene Routen unter `/api` | `serverPaths` von `registerStaticSite` ist per Default `["/api"]` |
| `registerStaticSite` nach den Routen | übernimmt den Not-Found-Handler, liefert `index.html` für SPA-Pfade; keinen eigenen `setNotFoundHandler` setzen |
| `registerShutdown` | sauberes Beenden bei `SIGTERM`/`SIGINT`; mit Datenbank `onClose: () => prisma.$disconnect()` |

Weitere Server-Pfade außerhalb `/api` (z. B. ein Webhook unter `/hooks`): in `serverPaths`
eintragen, `registerStaticSite(app, { staticDir, serverPaths: ["/api", "/hooks"] })`.

## Routen und Fehler

```ts
import type { FastifyInstance } from "fastify";
import { NotFoundError, ValidationError } from "@web-app-kit/backend";
import { findItem } from "../services/itemService.js";

export async function apiRoutes(app: FastifyInstance) {
  app.get<{ Params: { id: string } }>("/items/:id", async (request) => {
    const item = await findItem(request.params.id);
    if (!item) throw new NotFoundError("Item", request.params.id);
    return item;
  });
}
```

Fehlerklassen aus `@web-app-kit/backend`, nie `reply.code(…).send(…)` von Hand:

| Klasse | Status | Einsatz |
| --- | --- | --- |
| `BadRequestError` | 400 | Anfrage formal falsch |
| `UnauthorizedError` | 401 | Identität fehlt oder ungültig |
| `NotFoundError(resource, id)` | 404 | Datensatz fehlt |
| `ConflictError` | 409 | Zustand passt nicht (Duplikat, veraltete Version) |
| `ValidationError` | 422 | Eingabe fachlich ungültig |
| `TooManyRequestsError` | 429 | Zu viele Versuche, optional `Retry-After` |
| `UpstreamError` | 502 | Fremddienst antwortet fehlerhaft |
| `ConfigurationError` | (Startabbruch) | Konfiguration fehlt, Start soll scheitern |

Die Antwort ist `{ "error": "<Meldung>" }` mit dem Status der Klasse. Die Meldung ist Englisch
und dient dem Log und als letzter Fallback: Erwartete Fehler (Validierung 422, nicht gefunden
404, Konflikt 409) übersetzt das Frontend anhand des HTTP-Status mit i18n-Schlüsseln
(`localizeError` in `frontend.md`), die Backend-Meldung wird dafür nicht angezeigt. Trotzdem
klar und ohne interne Details formulieren. Alles andere wird als 500 mit
`"Internal server error"` beantwortet und nur im Log ausgegeben.

Unterscheidet das Frontend mehrere Fälle mit demselben Status (z. B. zwei Konflikte), bekommt
jeder Fall eine eigene Route oder eine eigene Prüfung im Frontend; die Meldung bleibt kein
Unterscheidungsmerkmal.

## Identität (optional)

Läuft das Tool hinter einem Login-Proxy, der den Header `x-amzn-oidc-data` setzt:

```ts
import { albIdentityConfigFromEnv, createAlbIdentityResolver } from "@web-app-kit/backend";

const resolveIdentity = createAlbIdentityResolver(albIdentityConfigFromEnv());
const identity = await resolveIdentity(request.headers);
```

Ohne `ALB_ARN`, `ALB_OIDC_ISSUER`, `ALB_OIDC_CLIENT_ID` (lokal) liefert der Resolver `null`.
Ein vorhandener, aber ungültiger Header wirft `UnauthorizedError`. Den Resolver als Option an
die Routen geben (`app.register(apiRoutes, { prefix: "/api", resolveIdentity })`), nicht
global speichern.

## Datenbank (optional)

Nur wenn das Tool Daten dauerhaft speichert. Muster der bestehenden Tools: Postgres 16 +
Prisma 7 (`@prisma/client`, `@prisma/adapter-pg`, `prisma`, `pg`), `src/db.ts` exportiert den
Client, `registerShutdown(app, { onClose: () => prisma.$disconnect() })`. Container-Start:
`prisma migrate deploy` vor dem Server (siehe `docker.md`). Ohne Speicherbedarf keine
Datenbank anlegen.

## Test (Pflicht, mindestens einer)

`src/api/version.test.ts`:

```ts
import { test } from "node:test";
import assert from "node:assert/strict";
import Fastify from "fastify";
import { registerErrorHandler, registerVersionRoute } from "@web-app-kit/backend";

test("GET /api/version returns app and kit version", async () => {
  const app = Fastify();
  registerErrorHandler(app);
  registerVersionRoute(app, { version: "v1.0.0" });
  const response = await app.inject({ method: "GET", url: "/api/version" });
  assert.equal(response.statusCode, 200);
  assert.equal(response.json().version, "v1.0.0");
  assert.match(response.json().kitVersion, /^\d+\.\d+\.\d+/);
});
```

Für eigene Routen genauso: App im Test aufbauen, `app.inject`, Statuscode und Body prüfen,
auch die Fehlerfälle (404, 422). Logik in `src/services/` als reine Funktionen halten, damit
sie ohne Server testbar ist.

Prüfen: `npm run typecheck && npm test` im Ordner `backend/`.
