#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
LINK="$SCRIPTS_DIR/link-agent-skills.sh"
KIT_SKILLS=vendor/web-app-kit/.agents/skills
LINK_BASE="../../$KIT_SKILLS"

expect_true() {
  local name="$1"
  shift
  if "$@"; then
    echo "ok   $name"
    return 0
  fi
  echo "FAIL $name"
  FAILURES=$((FAILURES + 1))
}

new_app() {
  local app="$WORK_ROOT/$1" skill
  shift
  for skill in "$@"; do
    mkdir -p "$app/$KIT_SKILLS/$skill"
    printf -- '---\nname: %s\n---\n' "$skill" > "$app/$KIT_SKILLS/$skill/SKILL.md"
  done
  echo "$app"
}

links_point_to_kit() {
  [ "$(readlink "$1/.agents/skills/alpha")" = "$LINK_BASE/alpha" ] \
    && [ "$(readlink "$1/.claude/skills/beta")" = "$LINK_BASE/beta" ] \
    && grep -q "name: beta" "$1/.claude/skills/beta/SKILL.md"
}

test_usage() {
  run_in "$WORK_ROOT" bash "$LINK" --force
  expect "unbekannte Option ergibt Exit 64" 64 "Usage: link-agent-skills.sh"
}

test_no_kit() {
  mkdir -p "$WORK_ROOT/empty"
  run_in "$WORK_ROOT/empty" bash "$LINK"
  expect "Exit 2 ohne Kit-Skills" 2 "Keine Kit-Skills unter $KIT_SKILLS"
}

test_fresh_and_idempotent() {
  local app
  app="$(new_app fresh alpha beta)"
  run_in "$app" bash "$LINK"
  expect "frisch: Links angelegt" 0 "angelegt: .claude/skills/beta -> $LINK_BASE/beta"
  expect_true "frisch: relative Links lösen auf" links_point_to_kit "$app"
  run_in "$app" bash "$LINK"
  expect "idempotent: zweiter Lauf meldet ok" 0 "ok: .agents/skills/alpha"
  expect_absent "idempotent: nichts neu angelegt" "angelegt:"
  run_in "$app" bash "$LINK" --check
  expect "--check ohne Abweichung" 0 "Alle Skill-Links aktuell."
}

test_check_missing() {
  local app
  app="$(new_app missing alpha beta)"
  bash -c "cd '$app' && bash '$LINK'" > /dev/null
  rm "$app/.claude/skills/alpha"
  run_in "$app" bash "$LINK" --check
  expect "--check meldet fehlenden Link" 1 "fehlt: .claude/skills/alpha"
  expect_true "--check legt nichts an" test ! -e "$app/.claude/skills/alpha"
}

test_stale_link() {
  local app
  app="$(new_app stale alpha)"
  mkdir -p "$app/.agents/skills"
  ln -s "$LINK_BASE/gamma" "$app/.agents/skills/gamma"
  ln -s ../elsewhere "$app/.agents/skills/own"
  run_in "$app" bash "$LINK" --check
  expect "--check meldet veralteten Link" 1 "veraltet: .agents/skills/gamma"
  run_in "$app" bash "$LINK"
  expect "veralteter Link wird entfernt" 0 "entfernt (veraltet): .agents/skills/gamma"
  expect_true "veralteter Link ist weg" test ! -L "$app/.agents/skills/gamma"
  expect_true "fremder Link bleibt" test -L "$app/.agents/skills/own"
}

test_conflict() {
  local app
  app="$(new_app conflict alpha beta)"
  mkdir -p "$app/.claude/skills/alpha"
  echo local > "$app/.claude/skills/alpha/SKILL.md"
  run_in "$app" bash "$LINK"
  expect "echter Ordner ergibt Konflikt" 3 "Konflikt: .claude/skills/alpha existiert"
  expect_true "Konflikt: Ordner unverändert" grep -qx local "$app/.claude/skills/alpha/SKILL.md"
  expect_true "Konflikt: nichts angelegt" test ! -e "$app/.agents/skills"
  run_in "$app" bash "$LINK" --check
  expect "--check zählt Konflikt als Abweichung" 1 "Konflikt: .claude/skills/alpha"
}

test_foreign_link() {
  local app
  app="$(new_app foreign alpha)"
  mkdir -p "$app/.agents/skills"
  ln -s ../somewhere/alpha "$app/.agents/skills/alpha"
  run_in "$app" bash "$LINK"
  expect "fremder Link gleichen Namens ergibt Konflikt" 3 "zeigt auf ../somewhere/alpha"
  expect_true "fremder Link bleibt erhalten" \
    test "$(readlink "$app/.agents/skills/alpha")" = ../somewhere/alpha
}

test_usage
test_no_kit
test_fresh_and_idempotent
test_check_missing
test_stale_link
test_conflict
test_foreign_link
finish
