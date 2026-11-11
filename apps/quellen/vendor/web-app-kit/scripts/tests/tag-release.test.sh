#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
TAG_RELEASE="$SCRIPTS_DIR/tag-release.sh"

fresh_kit() {
  KIT="$WORK_ROOT/$1"
  create_kit_remote "$1"
  cd "$KIT"
  write_kit_release 1.0.0 "${2:-keine}"
  commit_all "release 1.0.0"
  git push --quiet origin main
}

push_follow_up_commit() {
  echo "$1" > follow-up.txt
  commit_all "$1"
  git push --quiet origin main
}

test_missing_version_file() {
  fresh_kit noversion
  rm VERSION
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ohne VERSION" 1 "FEHLER: Datei VERSION fehlt im Repo-Root."
}

test_invalid_semver() {
  fresh_kit badsemver
  printf '1.0\n' > VERSION
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ungültige SemVer" 1 "FEHLER: VERSION '1.0' ist keine SemVer-Version (X.Y.Z)."
}

test_dirty_tree() {
  fresh_kit dirty
  echo change >> CHANGELOG.md
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert bei unsauberem Arbeitsbaum" 1 "FEHLER: Arbeitsbaum nicht sauber"
}

test_version_mismatch() {
  fresh_kit mismatch
  run_in "$KIT" bash "$TAG_RELEASE" v1.0.1
  expect "verweigert bei VERSION-Abweichung" 1 \
    "FEHLER: Angefragter Tag v1.0.1 passt nicht zu VERSION (v1.0.0)."
}

test_missing_changelog_entry() {
  fresh_kit nochangelog
  printf '1.1.0\n' > VERSION
  write_package backend 1.1.0
  write_package frontend 1.1.0
  write_package eslint-plugin 1.1.0
  commit_all "bump without changelog"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ohne CHANGELOG-Eintrag" 1 \
    "FEHLER: CHANGELOG.md hat keinen Eintrag '## [1.1.0]'."
}

test_invalid_app_action() {
  fresh_kit badaction vielleicht
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ungültige App-Aktion" 1 \
    "FEHLER: CHANGELOG-Eintrag 1.0.0 ohne gültige Zeile 'App-Aktion: keine|optional|Pflicht'."
}

test_package_version_mismatch() {
  fresh_kit packages
  write_package frontend 0.9.0
  commit_all "drift"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert bei package.json-Abweichung" 1 \
    "FEHLER: packages/frontend/package.json hat Version '0.9.0', VERSION ist 1.0.0."
}

test_eslint_plugin_version_mismatch() {
  fresh_kit pluginpackage
  write_package eslint-plugin 0.9.0
  commit_all "plugin drift"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert bei eslint-plugin-Abweichung" 1 \
    "FEHLER: packages/eslint-plugin/package.json hat Version '0.9.0', VERSION ist 1.0.0."
}

test_new_package_version_mismatch() {
  fresh_kit newpackage
  write_package extra 0.9.0
  commit_all "new package"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "prüft jedes Paket unter packages/" 1 \
    "FEHLER: packages/extra/package.json hat Version '0.9.0', VERSION ist 1.0.0."
}

test_missing_packages() {
  fresh_kit nopackages
  git rm --quiet -r packages
  commit_all "remove packages"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ohne Pakete" 1 "FEHLER: Keine Pakete gefunden (packages/*/package.json)."
}

test_missing_required_package() {
  fresh_kit norequired
  git rm --quiet -r packages/eslint-plugin
  commit_all "remove eslint-plugin"
  git push --quiet origin main
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert ohne Pflichtpaket" 1 "FEHLER: packages/eslint-plugin/package.json fehlt."
}

test_version_not_newer() {
  fresh_kit older
  git push --quiet origin "HEAD:refs/tags/v1.1.0"
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert, wenn VERSION nicht größer als neuester Tag" 1 \
    "FEHLER: VERSION 1.0.0 ist nicht größer als der neueste Tag v1.1.0."
}

test_head_ahead_of_remote() {
  fresh_kit ahead
  write_kit_release 1.1.0 optional
  commit_all "local only"
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert, wenn HEAD vor origin/main liegt" 1 "ist nicht origin/main"
}

test_head_behind_remote() {
  fresh_kit behind
  push_follow_up_commit "remote ahead"
  git reset --quiet --hard HEAD~1
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert, wenn HEAD hinter origin/main liegt" 1 "ist nicht origin/main"
}

test_tag_exists_remotely() {
  fresh_kit existing
  git push --quiet origin "HEAD:refs/tags/v1.0.0"
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert bei Tag auf dem Remote" 1 "FEHLER: Tag v1.0.0 existiert auf origin bereits."
}

test_local_tag_on_other_commit() {
  fresh_kit othercommit
  push_follow_up_commit "docs only"
  git tag v1.0.0 HEAD~1
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "verweigert bei lokalem Tag auf anderem Commit" 1 \
    "FEHLER: Tag v1.0.0 existiert lokal auf"
}

test_local_tag_rerun() {
  fresh_kit rerun
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "erster Lauf taggt lokal" 0 "git push origin refs/tags/v1.0.0"
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "zweiter Lauf erkennt lokalen Tag" 0 "liegt lokal bereits auf HEAD, fehlt aber auf origin"
  expect "zweiter Lauf nennt Push-Befehl" 0 "git push origin refs/tags/v1.0.0"
  run_in "$KIT" bash "$TAG_RELEASE" --push
  expect "Wiederaufnahme mit --push" 0 "Tag v1.0.0 nach origin gepusht."
}

test_happy_path_without_push() {
  fresh_kit happy
  run_in "$KIT" bash "$TAG_RELEASE"
  expect "taggt sauberen Stand" 0 "git push origin refs/tags/v1.0.0"
  run_in "$KIT" git cat-file -t v1.0.0
  expect "Tag ist annotiert" 0 "tag"
  run_in "$KIT" bash -c "git ls-remote --tags origin v1.0.0 | wc -l | tr -d ' '"
  expect "ohne --push nichts gepusht" 0 "0"
}

test_push_flag() {
  fresh_kit pushed
  run_in "$KIT" bash "$TAG_RELEASE" v1.0.0 --push
  expect "--push veröffentlicht den Tag" 0 "Tag v1.0.0 nach origin gepusht."
  run_in "$KIT" git ls-remote --tags origin v1.0.0
  expect "Tag liegt auf dem Remote" 0 "refs/tags/v1.0.0"
}

test_missing_version_file
test_invalid_semver
test_dirty_tree
test_version_mismatch
test_missing_changelog_entry
test_invalid_app_action
test_package_version_mismatch
test_eslint_plugin_version_mismatch
test_new_package_version_mismatch
test_missing_packages
test_missing_required_package
test_version_not_newer
test_head_ahead_of_remote
test_head_behind_remote
test_tag_exists_remotely
test_local_tag_on_other_commit
test_local_tag_rerun
test_happy_path_without_push
test_push_flag
finish
