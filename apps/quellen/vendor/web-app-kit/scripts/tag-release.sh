#!/usr/bin/env bash
set -euo pipefail

REMOTE="${RELEASE_REMOTE:-origin}"
BRANCH="${RELEASE_BRANCH:-main}"
REQUIRED_PACKAGES=(backend frontend eslint-plugin)

fail() {
  echo "FEHLER: $1" >&2
  exit 1
}

usage() {
  echo "Usage: scripts/tag-release.sh [vX.Y.Z] [--push]" >&2
  exit 2
}

parse_args() {
  REQUESTED_TAG=""
  SHOULD_PUSH=false
  for argument in "$@"; do
    case "$argument" in
      --push) SHOULD_PUSH=true ;;
      v[0-9]*) REQUESTED_TAG="$argument" ;;
      *) usage ;;
    esac
  done
}

read_version() {
  [ -f VERSION ] || fail "Datei VERSION fehlt im Repo-Root."
  VERSION="$(tr -d '[:space:]' < VERSION)"
  [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] \
    || fail "VERSION '$VERSION' ist keine SemVer-Version (X.Y.Z)."
  TAG="v$VERSION"
}

check_requested_tag() {
  [ -z "$REQUESTED_TAG" ] && return 0
  [ "$REQUESTED_TAG" = "$TAG" ] \
    || fail "Angefragter Tag $REQUESTED_TAG passt nicht zu VERSION ($TAG)."
}

fetch_remote() {
  git fetch --quiet --tags "$REMOTE" && return 0
  fail "git fetch --tags $REMOTE fehlgeschlagen. Ursachen: Netz/Zugang oder ein lokaler Tag,
  der vom gleichnamigen Remote-Tag abweicht (git tag -d <tag>, dann erneut)."
}

check_clean_tree() {
  [ -z "$(git status --porcelain)" ] \
    || fail "Arbeitsbaum nicht sauber (git status). Erst committen oder verwerfen."
}

check_head_on_remote_branch() {
  local head remote_head
  head="$(git rev-parse HEAD)"
  remote_head="$(git rev-parse --verify --quiet "refs/remotes/$REMOTE/$BRANCH")" \
    || fail "$REMOTE/$BRANCH nicht gefunden."
  [ "$head" = "$remote_head" ] && return 0
  fail "HEAD (${head:0:7}) ist nicht $REMOTE/$BRANCH (${remote_head:0:7}).
  Nur gemergte Stände taggen: git switch $BRANCH && git pull --ff-only."
}

check_tag_not_on_remote() {
  local remote_tags
  remote_tags="$(git ls-remote --tags "$REMOTE" "refs/tags/$TAG")" \
    || fail "git ls-remote $REMOTE fehlgeschlagen."
  [ -z "$remote_tags" ] || fail "Tag $TAG existiert auf $REMOTE bereits. VERSION anheben."
}

read_local_tag_state() {
  local tagged_commit
  HAS_LOCAL_TAG=false
  tagged_commit="$(git rev-parse --verify --quiet "refs/tags/$TAG^{commit}")" || return 0
  [ "$tagged_commit" = "$(git rev-parse HEAD)" ] \
    || fail "Tag $TAG existiert lokal auf ${tagged_commit:0:7}, nicht auf HEAD. VERSION anheben."
  HAS_LOCAL_TAG=true
}

newest_other_tag() {
  git tag -l 'v*' | { grep -E '^v[0-9]+\.[0-9]+\.[0-9]+$' || true; } \
    | { grep -vxF "$TAG" || true; } | sort -V | tail -1
}

check_version_is_newer() {
  local newest
  newest="$(newest_other_tag)"
  [ -z "$newest" ] && return 0
  [ "$(printf '%s\n%s\n' "$newest" "$TAG" | sort -V | tail -1)" = "$TAG" ] \
    || fail "VERSION $VERSION ist nicht größer als der neueste Tag $newest."
}

changelog_entry() {
  awk -v version="$VERSION" '
    index($0, "## [" version "]") == 1 { inside = 1; print; next }
    inside && /^## \[/ { exit }
    inside { print }
  ' CHANGELOG.md
}

check_changelog() {
  [ -f CHANGELOG.md ] || fail "CHANGELOG.md fehlt."
  local entry
  entry="$(changelog_entry)"
  [ -n "$entry" ] || fail "CHANGELOG.md hat keinen Eintrag '## [$VERSION]'."
  grep -qE '^App-Aktion: (keine|optional|Pflicht)' <<< "$entry" \
    || fail "CHANGELOG-Eintrag $VERSION ohne gültige Zeile 'App-Aktion: keine|optional|Pflicht'."
}

package_version() {
  sed -nE 's/^[[:space:]]*"version"[[:space:]]*:[[:space:]]*"([^"]+)".*/\1/p' "$1" | head -1
}

load_package_files() {
  shopt -s nullglob
  PACKAGE_FILES=(packages/*/package.json)
  shopt -u nullglob
  [ "${#PACKAGE_FILES[@]}" -gt 0 ] || fail "Keine Pakete gefunden (packages/*/package.json)."
  local name
  for name in "${REQUIRED_PACKAGES[@]}"; do
    [ -f "packages/$name/package.json" ] || fail "packages/$name/package.json fehlt."
  done
}

check_package_versions() {
  local file found
  load_package_files
  for file in "${PACKAGE_FILES[@]}"; do
    found="$(package_version "$file")"
    [ "$found" = "$VERSION" ] || fail "$file hat Version '$found', VERSION ist $VERSION."
  done
}

create_tag() {
  if [ "$HAS_LOCAL_TAG" = true ]; then
    echo "Tag $TAG liegt lokal bereits auf HEAD, fehlt aber auf $REMOTE."
    return 0
  fi
  git tag -a "$TAG" -m "web-app-kit $TAG"
  echo "Tag $TAG auf $(git rev-parse --short HEAD) angelegt."
}

publish_tag() {
  if [ "$SHOULD_PUSH" = true ]; then
    git push "$REMOTE" "refs/tags/$TAG"
    echo "Tag $TAG nach $REMOTE gepusht."
    return 0
  fi
  echo "Noch nicht gepusht. Veröffentlichen mit:"
  echo "  git push $REMOTE refs/tags/$TAG"
}

main() {
  parse_args "$@"
  cd "$(git rev-parse --show-toplevel)"
  read_version
  check_requested_tag
  fetch_remote
  check_clean_tree
  check_head_on_remote_branch
  check_tag_not_on_remote
  read_local_tag_state
  check_version_is_newer
  check_changelog
  check_package_versions
  create_tag
  publish_tag
}

main "$@"
