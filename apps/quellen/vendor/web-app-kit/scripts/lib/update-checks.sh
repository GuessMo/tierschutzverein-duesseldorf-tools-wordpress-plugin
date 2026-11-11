#!/usr/bin/env bash

CHECK_PROGRAMS=" grep rg find ls test "
SHELL_CHARACTERS='[;|&<>$`]'
FIND_ACTIONS='-exec|-execdir|-ok|-okdir|-delete|-fprint|-fprint0|-fprintf|-fls'
EXECUTING_OPTION="^($FIND_ACTIONS|--pre|--hostname-bin)(=.*)?$"

check_commands() {
  awk '
    /^## \[/ { version = substr($0, 5, index($0, "]") - 5) }
    /^[[:space:]]*(- )?Prüfen: `.+`[[:space:]]*$/ {
      line = $0
      sub(/^[^`]*`/, "", line)
      sub(/`[[:space:]]*$/, "", line)
      print version "\t" line
    }
  ' <<< "$1"
}

split_words() {
  local word
  WORDS=()
  xargs true <<< "$1" > /dev/null 2>&1 || return 1
  while IFS= read -r -d '' word; do
    WORDS+=("$word")
  done < <(xargs printf '%s\0' <<< "$1")
  [ "${#WORDS[@]}" -gt 0 ]
}

is_read_only() {
  local word
  if grep -q -- "$SHELL_CHARACTERS" <<< "$1"; then return 1; fi
  split_words "$1" || return 1
  [[ "$CHECK_PROGRAMS" == *" ${WORDS[0]} "* ]] || return 1
  for word in "${WORDS[@]}"; do
    if [[ "$word" =~ $EXECUTING_OPTION ]]; then return 1; fi
  done
  return 0
}

join_lines() {
  awk 'NR > 1 { printf ", " } { printf "%s", $0 } END { print "" }'
}

run_check() {
  local status=0 output
  output="$(cd "$APP_DIR" && "${WORDS[@]}" < /dev/null 2> "$CHECK_ERRORS")" || status=$?
  if [ -n "$output" ]; then
    echo "    betroffen: $(join_lines <<< "$output")"
  elif [ "${WORDS[0]}" = test ] && [ "$status" -eq 0 ]; then
    echo "    betroffen (Bedingung erfüllt)"
  elif grep -q "No such file or directory" "$CHECK_ERRORS"; then
    echo "    nicht betroffen (Pfad fehlt)"
  elif [ -s "$CHECK_ERRORS" ]; then
    echo "    Prüfung fehlgeschlagen: $(head -1 "$CHECK_ERRORS")"
  else
    echo "    nicht betroffen"
  fi
}

reject_check() {
  echo "    nicht ausgeführt: erlaubt sind nur grep, rg, find, ls, test ohne Shell-Zeichen"
  echo "    (; | & < > \$ \`) und ohne ausführende Optionen. Befehl von Hand prüfen."
}

run_update_checks() {
  local checks version command
  checks="$(check_commands "$1")"
  [ -n "$checks" ] || return 0
  CHECK_ERRORS="$(mktemp)"
  trap 'rm -f "$CHECK_ERRORS"' EXIT
  echo
  echo "Prüfbefehle aus den Umbau-Listen (ausgeführt in $APP_DIR):"
  while IFS=$'\t' read -r version command; do
    echo "  [$version] $command"
    if is_read_only "$command"; then run_check; else reject_check; fi
  done <<< "$checks"
}
