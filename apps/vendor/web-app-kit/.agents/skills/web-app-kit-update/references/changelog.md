# Changelog web-app-kit-update

- **1.1.0 (2026-11-05):** Ablauf auf die Umbau-Liste umgestellt (CHANGELOG-Blöcke `### Neu`
  und `### Umbau` ab Kit 1.0.0, Prüfbefehle in `check-kit-version.sh`): eine Aufgabe je
  Umbau-Punkt, Prüfbefehl vorher und nachher, Skript nach dem Submodule-Checkout erneut
  ausführen, älteste Version zuerst, Grenze beim Ziel-Tag. Ergänzt: Branch vom Remote-Stand,
  Vite-Cache, Dev-Container-Volumes per Label ermitteln und nur nach Rückfrage neu anlegen,
  `pre-push`-Pin-Hook, Rückweg bei Abbruch, Hinweis für andere Checkouts, gezieltes `git add`.
  Fehlerbilder, Container und Rückweg nach `references/` ausgelagert.
- **1.0.0 (2026-11-04):** Erste Fassung.
