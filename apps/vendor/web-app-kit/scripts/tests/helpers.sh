#!/usr/bin/env bash
set -euo pipefail

SCRIPTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORK_ROOT="$(cd "$(mktemp -d)" && pwd -P)"
trap 'rm -rf "$WORK_ROOT"' EXIT
FAILURES=0
OUTPUT=""
STATUS=0

export GIT_CONFIG_GLOBAL=/dev/null GIT_CONFIG_NOSYSTEM=1
export GIT_AUTHOR_NAME=test GIT_AUTHOR_EMAIL=test@example.invalid
export GIT_COMMITTER_NAME=test GIT_COMMITTER_EMAIL=test@example.invalid
export GIT_CONFIG_COUNT=2
export GIT_CONFIG_KEY_0=protocol.file.allow GIT_CONFIG_VALUE_0=always
export GIT_CONFIG_KEY_1=init.defaultBranch GIT_CONFIG_VALUE_1=main

run_in() {
  local directory="$1"
  shift
  set +e
  OUTPUT="$(cd "$directory" && "$@" 2>&1)"
  STATUS=$?
  set -e
}

expect() {
  local name="$1" expected_status="$2" expected_text="$3"
  if [ "$STATUS" -eq "$expected_status" ] && grep -qF -- "$expected_text" <<< "$OUTPUT"; then
    echo "ok   $name"
    return 0
  fi
  echo "FAIL $name (exit $STATUS, erwartet $expected_status, Text '$expected_text')"
  sed 's/^/     | /' <<< "$OUTPUT"
  FAILURES=$((FAILURES + 1))
}

expect_absent() {
  local name="$1" unexpected_text="$2"
  if ! grep -qF -- "$unexpected_text" <<< "$OUTPUT"; then
    echo "ok   $name"
    return 0
  fi
  echo "FAIL $name ('$unexpected_text' in der Ausgabe)"
  FAILURES=$((FAILURES + 1))
}

write_package() {
  mkdir -p "packages/$1"
  printf '{\n  "name": "@web-app-kit/%s",\n  "version": "%s"\n}\n' "$1" "$2" \
    > "packages/$1/package.json"
}

write_changelog_entry() {
  printf '## [%s] — 2026-10-09\n\n### Neu\n\n- %s\n\n### Umbau\n\n- keiner\n\n%s\n\n' \
    "$1" "$3" "$2" >> CHANGELOG.md
}

write_kit_release() {
  local version="$1" app_action="$2"
  printf '%s\n' "$version" > VERSION
  write_package backend "$version"
  write_package frontend "$version"
  write_package eslint-plugin "$version"
  local previous=""
  [ -f CHANGELOG.md ] && previous="$(cat CHANGELOG.md)"
  printf '# Changelog\n\n' > CHANGELOG.md
  write_changelog_entry "$version" "App-Aktion: $app_action" "Release $version"
  [ -n "$previous" ] && sed '1,2d' <<< "$previous" >> CHANGELOG.md
  return 0
}

commit_all() {
  git add -A
  git commit --quiet -m "$1"
}

create_kit_remote() {
  local name="$1"
  git init --quiet --bare "$WORK_ROOT/$name.git"
  git clone --quiet "$WORK_ROOT/$name.git" "$WORK_ROOT/$name" 2> /dev/null
}

finish() {
  [ "$FAILURES" -eq 0 ] && return 0
  echo "$FAILURES Test(s) fehlgeschlagen."
  exit 1
}
