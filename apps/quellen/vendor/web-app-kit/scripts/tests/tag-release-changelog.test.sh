#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
TAG_RELEASE="$SCRIPTS_DIR/tag-release.sh"

release_with_entry() {
  KIT="$WORK_ROOT/$1"
  create_kit_remote "$1"
  cd "$KIT"
  printf '%s\n' "$2" > VERSION
  write_package backend "$2"
  write_package frontend "$2"
  write_package eslint-plugin "$2"
  printf '# Changelog\n\n## [%s] — 2026-11-05\n\n%b\nApp-Aktion: %s\n' "$2" "$3" \
    "${4:-keine}" > CHANGELOG.md
  commit_all "release $2"
  git push --quiet origin main
}

test_missing_new_block() {
  release_with_entry noneu 1.1.0 '- Fix\n\n### Umbau\n\n- keiner\n'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ab 1.0.0 ohne Block Neu" 1 \
    "FEHLER: CHANGELOG-Eintrag 1.1.0 ohne Block '### Neu' (Pflicht ab 1.0.0)."
}

test_missing_rebuild_block() {
  release_with_entry noumbau 1.1.0 '### Neu\n\n- Fix\n'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ab 1.0.0 ohne Block Umbau" 1 \
    "FEHLER: CHANGELOG-Eintrag 1.1.0 ohne Block '### Umbau' (Pflicht ab 1.0.0)."
}

test_old_format_before_one() {
  release_with_entry oldformat 0.14.0 '- **Neu:** Fix\n'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "altes Format vor 1.0.0 bleibt erlaubt" 0 "git push origin refs/tags/v0.14.0"
}

test_blocks_present() {
  release_with_entry blocks 1.1.0 '### Neu\n\n- Fix\n\n### Umbau\n\n- keiner\n'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "taggt ab 1.0.0 mit beiden Blöcken" 0 "git push origin refs/tags/v1.1.0"
}

test_mandatory_without_step() {
  release_with_entry pflichtkeiner 2.0.0 '### Neu\n\n- Neu\n\n### Umbau\n\n- keiner\n' \
    'Pflicht (Umbau)'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert Pflicht mit Umbau nur keiner" 1 \
    "FEHLER: CHANGELOG-Eintrag 2.0.0 hat 'App-Aktion: Pflicht', aber keinen Umbau-Schritt."
}

test_mandatory_empty_block() {
  release_with_entry pflichtleer 2.0.0 '### Neu\n\n- Neu\n\n### Umbau\n' 'Pflicht (Umbau)'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert Pflicht mit leerem Umbau-Block" 1 \
    "FEHLER: CHANGELOG-Eintrag 2.0.0 hat 'App-Aktion: Pflicht', aber keinen Umbau-Schritt."
}

test_mandatory_with_step() {
  release_with_entry pflichtschritt 2.0.0 \
    '### Neu\n\n- Neu\n\n### Umbau\n\n- Importe umstellen.\n  Prüfen: `grep -rl x src`\n' \
    'Pflicht (Umbau)'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "taggt Pflicht mit Umbau-Schritt" 0 "git push origin refs/tags/v2.0.0"
}

test_mandatory_before_one() {
  release_with_entry pflichtalt 0.14.0 '- **Breaking:** alt\n' 'Pflicht (alt)'
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "Pflicht vor 1.0.0 ohne Umbau-Block bleibt erlaubt" 0 "git push origin refs/tags/v0.14.0"
}

test_missing_new_block
test_missing_rebuild_block
test_old_format_before_one
test_blocks_present
test_mandatory_without_step
test_mandatory_empty_block
test_mandatory_with_step
test_mandatory_before_one
finish
