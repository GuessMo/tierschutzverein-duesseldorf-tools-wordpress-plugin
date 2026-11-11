#!/usr/bin/env bash
set -euo pipefail

KIT_SKILLS="vendor/web-app-kit/.agents/skills"
LINK_BASE="../../$KIT_SKILLS"
TARGET_DIRS=".agents/skills .claude/skills"
RUN_COMMAND="bash vendor/web-app-kit/scripts/link-agent-skills.sh"
MODE=apply
DEVIATIONS=0
CONFLICTS=0
SKILL_NAMES=""

usage() {
  echo "Usage: link-agent-skills.sh [--check]" >&2
  echo "Exit: 0 aktuell, 1 Links fehlen/falsch (--check), 2 keine Kit-Skills, 3 Konflikt," >&2
  echo "      64 falscher Aufruf." >&2
  exit 64
}

parse_args() {
  [ "$#" -le 1 ] || usage
  case "${1:-}" in
    "") ;;
    --check) MODE=check ;;
    *) usage ;;
  esac
}

kit_skill_names() {
  local entry
  for entry in "$KIT_SKILLS"/*/; do
    if [ -d "$entry" ]; then basename "$entry"; fi
  done
}

require_kit_skills() {
  SKILL_NAMES="$(kit_skill_names)"
  [ -n "$SKILL_NAMES" ] && return 0
  echo "Keine Kit-Skills unter $KIT_SKILLS gefunden." >&2
  echo "Im Wurzelordner des App-Repos ausführen (git submodule update --init?)." >&2
  exit 2
}

link_state() {
  local path="$1" expected="$2"
  if [ -L "$path" ]; then
    [ "$(readlink "$path")" = "$expected" ] && echo ok || echo foreign
    return 0
  fi
  [ -e "$path" ] && echo conflict || echo missing
}

report_conflict() {
  local path="$1" expected="$2"
  case "$(link_state "$path" "$expected")" in
    conflict) echo "Konflikt: $path existiert als Datei oder Ordner." >&2 ;;
    foreign) echo "Konflikt: $path zeigt auf $(readlink "$path"), erwartet $expected." >&2 ;;
    *) return 0 ;;
  esac
  CONFLICTS=$((CONFLICTS + 1))
}

report_conflicts() {
  local dir name
  for dir in $TARGET_DIRS; do
    for name in $SKILL_NAMES; do
      report_conflict "$dir/$name" "$LINK_BASE/$name"
    done
  done
}

stop_on_conflicts() {
  [ "$CONFLICTS" -eq 0 ] && return 0
  if [ "$MODE" = check ]; then
    DEVIATIONS=$((DEVIATIONS + CONFLICTS))
    return 0
  fi
  echo "Abbruch: $CONFLICTS Konflikt(e), nichts geändert." >&2
  echo "Gleichnamige Datei, Ordner oder fremden Link prüfen und entfernen, dann erneut" \
    "ausführen." >&2
  exit 3
}

is_kit_skill() {
  local candidate
  for candidate in $SKILL_NAMES; do
    [ "$candidate" = "$1" ] && return 0
  done
  return 1
}

remove_link() {
  if [ "$MODE" = check ]; then
    echo "veraltet: $1"
    DEVIATIONS=$((DEVIATIONS + 1))
    return 0
  fi
  rm "$1"
  echo "entfernt (veraltet): $1"
}

remove_stale_links() {
  local dir path name
  for dir in $TARGET_DIRS; do
    [ -d "$dir" ] || continue
    for path in "$dir"/*; do
      [ -L "$path" ] || continue
      name="$(basename "$path")"
      [ "$(readlink "$path")" = "$LINK_BASE/$name" ] || continue
      is_kit_skill "$name" && continue
      remove_link "$path"
    done
  done
}

create_link() {
  if [ "$MODE" = check ]; then
    echo "fehlt: $1"
    DEVIATIONS=$((DEVIATIONS + 1))
    return 0
  fi
  mkdir -p "$(dirname "$1")"
  ln -s "$2" "$1"
  echo "angelegt: $1 -> $2"
}

link_skills() {
  local dir name
  for dir in $TARGET_DIRS; do
    for name in $SKILL_NAMES; do
      case "$(link_state "$dir/$name" "$LINK_BASE/$name")" in
        ok) echo "ok: $dir/$name" ;;
        missing) create_link "$dir/$name" "$LINK_BASE/$name" ;;
      esac
    done
  done
}

finish() {
  if [ "$MODE" = apply ]; then
    echo "Skill-Links aktuell."
    exit 0
  fi
  if [ "$DEVIATIONS" -eq 0 ]; then
    echo "Alle Skill-Links aktuell."
    exit 0
  fi
  echo "$DEVIATIONS Abweichung(en). Beheben mit: $RUN_COMMAND"
  exit 1
}

main() {
  parse_args "$@"
  require_kit_skills
  report_conflicts
  stop_on_conflicts
  remove_stale_links
  link_skills
  finish
}

main "$@"
