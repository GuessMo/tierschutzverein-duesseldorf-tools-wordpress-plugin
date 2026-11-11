# Vorlage: `AGENTS.md` des neuen Tools

In den Repo-Wurzelordner des Tools als `AGENTS.md` kopieren, Platzhalter ersetzen, Abschnitt
„Fachliche Regeln" mit tool-spezifischen Regeln füllen oder streichen. Für Agenten, die
`CLAUDE.md` statt `AGENTS.md` lesen, zusätzlich `CLAUDE.md` mit der Zeile `@AGENTS.md`
anlegen.

```markdown
# <Tool-Name> — Agent-Regeln

<Ein Satz: was das Tool tut und für wen.>
Stack: Fastify (Backend, Port <BACKEND_PORT>), React + Vite + Tailwind v4 (Frontend,
Port <FRONTEND_PORT>), UI und Server-Helfer aus web-app-kit (`vendor/web-app-kit`, Git-
Submodule auf Release-Tag). Agent-neutral.

## Skills

Die Skills des Kits liegen als Links unter `.agents/skills/` und `.claude/skills/`:

- `web-app-kit-new-tool` — Aufbau, Container, Checkliste
- `web-app-kit-ui` — UI nur mit Kit-Komponenten, Zustände, WCAG 2.2 AA, Theme
- `web-app-kit-update` — Kit-Pin anheben

Links fehlen oder sind veraltet: `bash vendor/web-app-kit/scripts/link-agent-skills.sh`
(Prüfung ohne Änderung: `--check`).

## Regeln

- UI ausschließlich aus `@web-app-kit/frontend/ui`, Icons nur `@tabler/icons-react`. Fehlt ein
  Baustein, kommt er in den Kit, nicht als lokale Kopie und nicht aus einer zweiten
  Komponenten-Bibliothek.
- Keine fest eingebauten Texte: alles über `src/i18n` (Deutsch und Englisch).
- `npm run lint` im Frontend ohne Fehler (`@web-app-kit/eslint-plugin`, Pflicht).
- Backend-Fehler nur über die Fehlerklassen aus `@web-app-kit/backend`. Im Frontend hält der
  Fehler-State das Fehlerobjekt oder einen i18n-Schlüssel; übersetzt wird beim Rendern mit
  `localizeError(error, t)`.
- WCAG 2.2 AA in Hell und Dunkel.
- Kit nie im Submodule ändern. Kit-Update: Skill `web-app-kit-update`.

## Prüfen vor jedem Commit

- `backend/`: `npm run typecheck && npm test`
- `frontend/`: `npm run lint && npm run build`
- Container (nach dem ersten Commit):
  `docker build --build-arg APP_VERSION="$(git describe --tags --always 2>/dev/null || echo dev)" .`

## Fachliche Regeln

- <Regel>

## Deploy

Siehe `.agents/skills/web-app-kit-new-tool/references/deploy.md` (vom Team gepflegt).
```
