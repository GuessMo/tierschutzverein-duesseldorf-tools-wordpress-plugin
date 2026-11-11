# Deploy

Diese Datei pflegt jedes Team selbst, im eigenen web-app-kit-Repository (nicht im Submodule
eines Tools). Änderungen erreichen die Tools mit dem nächsten Kit-Release. Der Kit gibt nur
die allgemeinen Anforderungen vor; wohin und wie ein Tool ausgerollt wird, steht im Abschnitt
„Deploy-Ziel dieses Teams".

## Allgemeine Anforderungen an jedes Tool

| Punkt | Anforderung |
| --- | --- |
| Artefakt | Ein Container-Image aus dem `Dockerfile` im Repo-Wurzelordner (siehe `docker.md`) |
| Version | Build mit voller Tag-Historie, `--build-arg APP_VERSION="$(git describe --tags --always)"` |
| Port | Der Container lauscht auf `PORT` (Default im Image `<BACKEND_PORT>`), `0.0.0.0` |
| Health-Check | `GET /api/version` (200, JSON, `cache-control: no-store`) oder `GET /` (200, `index.html`) |
| Beenden | `SIGTERM` beendet sauber (`registerShutdown`, Timeout 10 s) |
| Konfiguration | nur über Umgebungsvariablen, keine Secrets im Image oder Repo |
| Logs | Fastify-Logger auf stdout (JSON) |

Umgebungsvariablen, die der Kit liest:

| Variable | Pflicht | Wirkung |
| --- | --- | --- |
| `PORT` | nein | Port des Servers |
| `STATIC_DIR` | im Image gesetzt | Ordner des gebauten Frontends (`/app/public`) |
| `APP_VERSION` | im Image gesetzt | Inhalt von `GET /api/version` |
| `CORS_ORIGIN` | nein | erlaubte Herkünfte, kommagetrennt (nur nötig, wenn das Frontend nicht vom selben Server kommt) |
| `ALB_ARN`, `ALB_OIDC_ISSUER`, `ALB_OIDC_CLIENT_ID` | nur mit Login-Proxy | Prüfung des Identitäts-Headers (`createAlbIdentityResolver`) |
| `DATABASE_URL` | nur mit Datenbank | Verbindung zu Postgres |

Live-Prüfung nach jedem Deploy: Footer der App bzw. `GET /api/version` zeigt denselben Wert
wie `git describe --tags` auf dem ausgerollten Commit.

## Deploy-Ziel dieses Teams

Deploy-Ziel dieses Teams: hier eintragen.

| Punkt | Wert |
| --- | --- |
| Registry (wohin das Image gepusht wird) | hier eintragen |
| Pipeline (Datei, Trigger, Branches) | hier eintragen |
| Laufzeit (Plattform, Ressourcen, Health-Check-Konfiguration) | hier eintragen |
| Zugang (Login, Netz, Berechtigungen) | hier eintragen |
| Secrets (wo gepflegt, wie injiziert) | hier eintragen |
| Weitere Doku | hier eintragen |

Solange hier nichts eingetragen ist: vor jedem Deploy-Schritt beim Team nachfragen, nichts
annehmen.
