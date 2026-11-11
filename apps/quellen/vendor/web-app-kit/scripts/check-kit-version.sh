#!/usr/bin/env bash
set -euo pipefail

KIT_SUBMODULE="${WEB_APP_KIT_SUBMODULE:-vendor/web-app-kit}"
MANDATORY_PATTERN='^App-Aktion: Pflicht'

usage() {
  echo "Usage: check-kit-version.sh [app-repo-path]" >&2
  echo "Exit: 0 aktuell, 1 veraltet/Pin ungetaggt, 2 kein Kit-Submodule, 3 keine Tags," >&2
  echo "      4 kein Git-Repo, 5 Pin neuer als Remote, 64 falscher Aufruf." >&2
  exit 64
}

resolve_paths() {
  if ! APP_DIR="$(cd "$1" 2> /dev/null && git rev-parse --show-toplevel 2> /dev/null)"; then
    echo "Kein Git-Repo: $1" >&2
    exit 4
  fi
  KIT_DIR="$APP_DIR/$KIT_SUBMODULE"
  [ -e "$KIT_DIR/.git" ] && return 0
  echo "Kein Kit-Submodule unter $KIT_DIR (git submodule update --init?)." >&2
  exit 2
}

read_pinned_commit() {
  PINNED_COMMIT="$(git -C "$APP_DIR" ls-tree HEAD "$KIT_SUBMODULE" 2> /dev/null \
    | awk '{print $3}' || true)"
  if [ -z "$PINNED_COMMIT" ]; then
    echo "Kit-Submodule ist noch nicht committet; nehme den ausgecheckten Stand." >&2
    PINNED_COMMIT="$(git -C "$KIT_DIR" rev-parse HEAD)"
  fi
}

sorted_versions() {
  { grep -E '^v[0-9]+\.[0-9]+\.[0-9]+$' || true; } | sort -V
}

remote_tag_names() {
  git -C "$KIT_DIR" ls-remote --tags --refs origin 'v*' | awk '{print $2}' \
    | sed 's#^refs/tags/##'
}

load_tags() {
  local remote_tags
  if remote_tags="$(remote_tag_names 2> /dev/null)"; then
    git -C "$KIT_DIR" fetch --quiet --tags origin \
      || echo "WARNUNG: Tag-Fetch fehlgeschlagen, CHANGELOG ggf. veraltet." >&2
    NEWEST_TAG="$(sorted_versions <<< "$remote_tags" | tail -1)"
    return 0
  fi
  echo "WARNUNG: Kit-Remote nicht erreichbar. Ergebnis basiert auf lokalen Tags." >&2
  NEWEST_TAG="$(git -C "$KIT_DIR" tag -l 'v*' | sorted_versions | tail -1)"
}

read_pinned_tag() {
  PINNED_TAG="$(git -C "$KIT_DIR" tag --points-at "$PINNED_COMMIT" -l 'v*' \
    | sorted_versions | tail -1)"
}

changelog_entries_between() {
  local from="${1#v}" to="${2#v}"
  git -C "$KIT_DIR" show "$2:CHANGELOG.md" 2> /dev/null | awk -v from="$from" -v to="$to" '
    function newer(a, b,   x, y, i) {
      split(a, x, "."); split(b, y, ".")
      for (i = 1; i <= 3; i++) if (x[i] + 0 != y[i] + 0) return x[i] + 0 > y[i] + 0
      return 0
    }
    /^## \[/ {
      version = substr($0, 5, index($0, "]") - 5)
      inside = newer(version, from) && !newer(version, to)
    }
    inside { print }
  '
}

print_entries() {
  local entries
  entries="$(changelog_entries_between "$1" "$2" || true)"
  if [ -z "$entries" ]; then
    echo "Keine CHANGELOG-Einträge gefunden (CHANGELOG.md in $2 fehlt?)."
    return 0
  fi
  echo "CHANGELOG $1 -> $2:"
  sed -E "s/$MANDATORY_PATTERN/!! &/" <<< "$entries"
  grep -qE "$MANDATORY_PATTERN" <<< "$entries" || return 0
  echo
  echo "!! Mindestens ein Eintrag verlangt App-Aktion: Pflicht (Umbau-Liste oben abarbeiten)."
}

print_update_steps() {
  local kit app
  kit="$(printf '%q' "$KIT_DIR")"
  app="$(printf '%q' "$APP_DIR")"
  echo
  echo "Nachziehen:"
  echo "  git -C $kit fetch --tags && git -C $kit checkout $NEWEST_TAG"
  echo "  Kopie und Lockfile erneuern (Kit-README, Abschnitt Nutzung), App bauen und testen"
  echo "  git -C $app add $KIT_SUBMODULE && git -C $app commit"
}

is_newer_than() {
  [ "$1" != "$2" ] && [ "$(printf '%s\n%s\n' "$1" "$2" | sort -V | tail -1)" = "$1" ]
}

report() {
  echo "Kit gepinnt:  ${PINNED_TAG:-ungetaggt (${PINNED_COMMIT:0:7})}"
  echo "Kit neuester: $NEWEST_TAG"
  if [ -z "$PINNED_TAG" ]; then
    echo "Pin ist kein Release-Tag. Auf $NEWEST_TAG pinnen."
    exit 1
  fi
  if [ "$PINNED_TAG" = "$NEWEST_TAG" ]; then
    echo "Aktuell."
    exit 0
  fi
  if is_newer_than "$PINNED_TAG" "$NEWEST_TAG"; then
    echo "Pin neuer als Remote: $PINNED_TAG ist auf dem Kit-Remote nicht veröffentlicht."
    exit 5
  fi
  echo "VERALTET."
  print_entries "$PINNED_TAG" "$NEWEST_TAG"
  print_update_steps
  exit 1
}

main() {
  [ "$#" -le 1 ] || usage
  case "${1:-}" in -h | --help) usage ;; esac
  resolve_paths "${1:-.}"
  read_pinned_commit
  load_tags
  if [ -z "$NEWEST_TAG" ]; then
    echo "Keine Kit-Tags gefunden (weder remote noch lokal)." >&2
    exit 3
  fi
  read_pinned_tag
  report
}

main "$@"
