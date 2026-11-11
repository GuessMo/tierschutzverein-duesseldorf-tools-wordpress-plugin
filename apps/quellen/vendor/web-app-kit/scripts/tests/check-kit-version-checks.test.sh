#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
CHECK="$SCRIPTS_DIR/check-kit-version.sh"
SUBMODULE=vendor/web-app-kit
KIT="$WORK_ROOT/kit"
APP="$WORK_ROOT/app"

UMBAU_ENTRY='## [1.0.0] — 2026-11-05

### Neu

- Baustein X im Kit.

### Umbau

- Lokale UI-Kopien löschen.
  Prüfen: `grep -rl "components/ui/" frontend/src`
- Alte Datei entfernen.
  Prüfen: `test -f legacy.txt`
- Ordner aufräumen.
  Prüfen: `rm -rf frontend`
- Zählen.
  Prüfen: `grep -rl x frontend | wc -l`
- Suchen und löschen.
  Prüfen: `find frontend -name "*.tsx" -delete`
- Vorverarbeiten.
  Prüfen: `rg --pre=cat x frontend`
- Fehlender Ordner.
  Prüfen: `grep -rl x does-not-exist`

App-Aktion: Pflicht (Umbau-Liste abarbeiten)
'

release_umbau_kit() {
  local previous
  previous="$(sed '1,2d' CHANGELOG.md)"
  printf '1.0.0\n' > VERSION
  printf '# Changelog\n\n%s\n%s\n' "$UMBAU_ENTRY" "$previous" > CHANGELOG.md
  commit_all "release 1.0.0"
  git tag v1.0.0
  git push --quiet origin main v1.0.0
}

setup_kit() {
  create_kit_remote kit
  cd "$KIT"
  write_kit_release 0.1.0 keine
  printf '# Changelog\n\n## [0.1.0] — 2026-10-07\n\n- Start\n\nApp-Aktion: keine\n\n' \
    > CHANGELOG.md
  commit_all "release 0.1.0"
  git tag v0.1.0
  git push --quiet origin main v0.1.0
  printf '# Changelog\n\n## [0.2.0] — 2026-10-08\n\n- **Neu:** altes Format\n\n%s\n' \
    "App-Aktion: optional" > CHANGELOG.new
  sed '1,2d' CHANGELOG.md >> CHANGELOG.new
  mv CHANGELOG.new CHANGELOG.md
  printf '0.2.0\n' > VERSION
  commit_all "release 0.2.0"
  git tag v0.2.0
  git push --quiet origin main v0.2.0
  release_umbau_kit
}

setup_app() {
  git init --quiet "$APP"
  cd "$APP"
  git submodule --quiet add "$WORK_ROOT/kit.git" "$SUBMODULE" 2> /dev/null
  git -C "$SUBMODULE" checkout --quiet v0.1.0
  git add -A
  git commit --quiet -m "pin v0.1.0"
  mkdir -p frontend/src
  echo 'import { Button } from "@/components/ui/button";' > frontend/src/a.tsx
  echo 'export const b = 1;' > frontend/src/b.tsx
}

test_shows_old_and_new_entries() {
  run_in "$APP" bash "$CHECK"
  expect "Exit-Code bleibt 1 bei veraltetem Pin" 1 "VERALTET."
  expect "zeigt alte Einträge weiter an" 1 "**Neu:** altes Format"
  expect "zeigt den Block Umbau" 1 "### Umbau"
  expect "nennt den Abschnitt der Prüfbefehle" 1 "Prüfbefehle aus den Umbau-Listen"
}

test_lists_affected_files() {
  run_in "$APP" bash "$CHECK"
  expect "grep-Treffer als betroffen" 1 "betroffen: frontend/src/a.tsx"
  expect_absent "nicht passende Datei fehlt" "frontend/src/b.tsx"
  expect "test ohne Treffer ist nicht betroffen" 1 "nicht betroffen"
  expect "fehlender Pfad gilt als nicht betroffen" 1 "nicht betroffen (Pfad fehlt)"
}

test_condition_met() {
  touch "$APP/legacy.txt"
  run_in "$APP" bash "$CHECK"
  expect "erfüllte test-Bedingung ist betroffen" 1 "betroffen (Bedingung erfüllt)"
  rm "$APP/legacy.txt"
}

test_rejects_unsafe_commands() {
  run_in "$APP" bash "$CHECK"
  expect "rm wird nicht ausgeführt" 1 "[1.0.0] rm -rf frontend"
  expect "Hinweis bei nicht erlaubtem Befehl" 1 "nicht ausgeführt: erlaubt sind nur grep"
  run_in "$APP" bash -c "grep -c 'nicht ausgeführt' <<< \"\$(bash '$CHECK')\""
  expect "rm, Pipe, find -delete und rg --pre abgewiesen" 0 "4"
  run_in "$APP" ls frontend/src
  expect "Arbeitsbaum der App unverändert" 0 "a.tsx"
}

test_not_affected_after_rebuild() {
  echo 'import { Button } from "@web-app-kit/frontend/ui";' > "$APP/frontend/src/a.tsx"
  run_in "$APP" bash "$CHECK"
  expect_absent "nach dem Umbau keine Treffer" "betroffen: frontend/src/a.tsx"
}

setup_kit
setup_app
test_shows_old_and_new_entries
test_lists_affected_files
test_condition_met
test_rejects_unsafe_commands
test_not_affected_after_rebuild
finish
