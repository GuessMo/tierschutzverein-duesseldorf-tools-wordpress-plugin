#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
CHECK="$SCRIPTS_DIR/check-kit-version.sh"
SUBMODULE=vendor/web-app-kit
MANDATORY_FOOTER="Mindestens ein Eintrag verlangt App-Aktion: Pflicht"

release_kit() {
  cd "$KIT"
  write_kit_release "$1" "$2"
  commit_all "release $1"
  git tag "v$1"
  git push --quiet origin main "v$1"
}

setup_kit() {
  KIT="$WORK_ROOT/kit"
  create_kit_remote kit
  release_kit 0.1.0 keine
  release_kit 0.2.0 "optional; neue Funktion"
  release_kit 1.0.0 "Pflicht; Umbau-Liste: X ersetzen"
  release_kit 1.0.1 keine
}

pin_app() {
  cd "$APP/$SUBMODULE"
  git checkout --quiet "$1"
  cd "$APP"
  git add -A
  git commit --quiet --allow-empty -m "pin $1"
}

new_app() {
  git init --quiet "$1"
  cd "$1"
  git submodule --quiet add "$2" "$SUBMODULE" 2> /dev/null
}

setup_app() {
  APP="$WORK_ROOT/app"
  new_app "$APP" "$WORK_ROOT/kit.git"
  pin_app v0.1.0
}

test_usage() {
  run_in "$APP" bash "$CHECK" one two
  expect "falscher Aufruf ergibt Exit 64" 64 "Usage: check-kit-version.sh"
}

test_not_a_git_repo() {
  mkdir -p "$WORK_ROOT/not-git"
  run_in "$WORK_ROOT" bash "$CHECK" "$WORK_ROOT/not-git"
  expect "Exit 4 ohne Git-Repo" 4 "Kein Git-Repo: $WORK_ROOT/not-git"
}

test_no_submodule() {
  git init --quiet "$WORK_ROOT/plain"
  run_in "$WORK_ROOT/plain" bash "$CHECK"
  expect "Exit 2 ohne Kit-Submodule" 2 "Kein Kit-Submodule"
}

test_no_tags() {
  create_kit_remote untagged-kit
  cd "$WORK_ROOT/untagged-kit"
  write_kit_release 0.1.0 keine
  commit_all "no tag"
  git push --quiet origin main
  new_app "$WORK_ROOT/untagged-app" "$WORK_ROOT/untagged-kit.git"
  run_in "$WORK_ROOT/untagged-app" bash "$CHECK"
  expect "Exit 3 ohne Kit-Tags" 3 "Keine Kit-Tags gefunden (weder remote noch lokal)."
}

test_uncommitted_pin() {
  new_app "$WORK_ROOT/fresh-app" "$WORK_ROOT/kit.git"
  git -C "$SUBMODULE" checkout --quiet v1.0.1
  run_in "$WORK_ROOT/fresh-app" bash "$CHECK"
  expect "nicht committeter Pin nutzt ausgecheckten Stand" 0 "noch nicht committet"
}

test_outdated_with_mandatory() {
  pin_app v0.1.0
  run_in "$APP" bash "$CHECK"
  expect "veraltet erkannt" 1 "VERALTET."
  expect "zeigt Einträge nach dem Pin" 1 "## [0.2.0]"
  expect "zeigt die Obergrenze" 1 "## [1.0.1]"
  expect "hebt Pflicht hervor" 1 "!! App-Aktion: Pflicht"
  expect "Pflicht-Hinweis am Ende" 1 "$MANDATORY_FOOTER"
  expect "Befehle mit absolutem Pfad" 1 "git -C $APP/$SUBMODULE checkout v1.0.1"
  expect_absent "listet den Pin-Eintrag nicht" "## [0.1.0]"
}

test_path_argument() {
  pin_app v0.1.0
  run_in "$WORK_ROOT" bash "$CHECK" "$APP"
  expect "App-Pfad als Argument" 1 "Kit neuester: v1.0.1"
}

test_offline_fallback() {
  pin_app v0.1.0
  git -C "$APP/$SUBMODULE" remote set-url origin "$WORK_ROOT/missing.git"
  run_in "$APP" bash "$CHECK"
  expect "warnt ohne Remote" 1 "WARNUNG: Kit-Remote nicht erreichbar"
  git -C "$APP/$SUBMODULE" remote set-url origin "$WORK_ROOT/kit.git"
}

test_outdated_without_mandatory() {
  pin_app v1.0.0
  run_in "$APP" bash "$CHECK"
  expect "veraltet ohne Pflicht" 1 "## [1.0.1]"
  expect_absent "kein Pflicht-Hinweis" "$MANDATORY_FOOTER"
}

test_current() {
  pin_app v1.0.1
  run_in "$APP" bash "$CHECK"
  expect "aktuell erkannt" 0 "Aktuell."
}

test_pin_newer_than_remote() {
  pin_app v1.0.1
  git -C "$APP/$SUBMODULE" tag v9.0.0
  run_in "$APP" bash "$CHECK"
  expect "Pin neuer als Remote" 5 "Pin neuer als Remote: v9.0.0"
  expect_absent "nicht als veraltet gemeldet" "VERALTET."
  git -C "$APP/$SUBMODULE" tag -d v9.0.0 > /dev/null
}

test_untagged_pin() {
  cd "$KIT"
  git switch --quiet -c untagged
  echo extra > extra.txt
  commit_all "untagged"
  git push --quiet origin untagged
  git -C "$APP/$SUBMODULE" fetch --quiet origin
  pin_app origin/untagged
  run_in "$APP" bash "$CHECK"
  expect "ungetaggter Pin gemeldet" 1 "Pin ist kein Release-Tag. Auf v1.0.1 pinnen."
}

setup_kit
setup_app
test_usage
test_not_a_git_repo
test_no_submodule
test_no_tags
test_uncommitted_pin
test_outdated_with_mandatory
test_path_argument
test_offline_fallback
test_outdated_without_mandatory
test_current
test_pin_newer_than_remote
test_untagged_pin
finish
