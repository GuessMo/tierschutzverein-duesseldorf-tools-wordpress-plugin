#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "${BASH_SOURCE[0]}")/helpers.sh"
CREATE="$SCRIPTS_DIR/create-favicon.mjs"
KIT_BRAND="$SCRIPTS_DIR/../packages/frontend/src/brand.css"
ICON="$WORK_ROOT/icon.svg"

write_brand() {
  printf ':root {\n    --primary: %s;\n    --primary-foreground: oklch(1 0 0);\n}\n\n' "$1" \
    > "$WORK_ROOT/brand.css"
  printf '.dark {\n    --primary: oklch(0.7 0.2 355);\n    --primary-foreground: %s;\n}\n' "$2" \
    >> "$WORK_ROOT/brand.css"
}

write_icon() {
  printf '<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s" fill="none" ' "$1" > "$ICON"
  printf 'stroke="currentColor" stroke-width="2"><path d="M12 5v14" /></svg>\n' >> "$ICON"
}

test_usage() {
  run_in "$WORK_ROOT" node "$CREATE"
  expect "ohne --text/--icon ergibt Exit 64" 64 "Usage: create-favicon.mjs"
  run_in "$WORK_ROOT" node "$CREATE" --text A --icon "$ICON"
  expect "--text und --icon zusammen ergibt Exit 64" 64 "Usage: create-favicon.mjs"
}

test_default_output_uses_kit_brand() {
  mkdir -p "$WORK_ROOT/tool"
  run_in "$WORK_ROOT/tool" node "$CREATE" --text W
  expect "schreibt frontend/public/favicon.svg" 0 "Favicon geschrieben: frontend/public/favicon.svg"
  local primary
  primary="$(sed -n '/^:root/,/^}/s/^ *--primary: \(.*\);$/\1/p' "$KIT_BRAND")"
  OUTPUT="$(cat "$WORK_ROOT/tool/frontend/public/favicon.svg")"
  expect "Kachel hell aus brand.css des Kits" 0 ".tile { fill: $primary; }"
  expect "Kürzel als Text" 0 'font-size="20" fill="currentColor">W</text>'
}

test_brand_colors_per_mode() {
  write_brand "oklch(0.55 0.22 1)" "oklch(0.2 0 0)"
  run_in "$WORK_ROOT" node "$CREATE" --text AB --brand brand.css --out out/fav.svg
  OUTPUT="$(cat "$WORK_ROOT/out/fav.svg")"
  expect "Kachel hell" 0 ".tile { fill: oklch(0.55 0.22 1); }"
  expect "Zeichen hell" 0 ".glyph { color: oklch(1 0 0); }"
  expect "Umschaltung per prefers-color-scheme" 0 "@media (prefers-color-scheme: dark)"
  expect "Kachel dunkel" 0 ".tile { fill: oklch(0.7 0.2 355); }"
  expect "Zeichen dunkel" 0 ".glyph { color: oklch(0.2 0 0); }"
  expect "zwei Zeichen kleiner" 0 'font-size="15" fill="currentColor">AB</text>'
  expect_absent "keine offenen Platzhalter" "{{"
}

test_text_is_escaped() {
  write_brand "oklch(0.55 0.22 1)" "oklch(0.2 0 0)"
  run_in "$WORK_ROOT" node "$CREATE" --text "<" --brand brand.css --out esc.svg
  OUTPUT="$(cat "$WORK_ROOT/esc.svg")"
  expect "Sonderzeichen maskiert" 0 ">&lt;</text>"
}

test_rejects_long_text() {
  run_in "$WORK_ROOT" node "$CREATE" --text ABC --out long.svg
  expect "drei Zeichen abgelehnt" 1 "--text braucht 1 oder 2 Zeichen"
}

test_rejects_non_oklch() {
  write_brand "#e5007d" "oklch(0.2 0 0)"
  run_in "$WORK_ROOT" node "$CREATE" --text A --brand brand.css --out hex.svg
  expect "Hex in brand.css abgelehnt" 1 "--primary ist kein oklch(L C H)-Wert: #e5007d"
  write_brand "oklch(0.55 0.22 1)" "var(--x)"
  run_in "$WORK_ROOT" node "$CREATE" --text A --brand brand.css --out var.svg
  expect "var() in brand.css abgelehnt" 1 "--primary-foreground ist kein oklch(L C H)-Wert"
}

test_icon() {
  write_brand "oklch(0.55 0.22 1)" "oklch(0.2 0 0)"
  write_icon "0 0 24 24"
  run_in "$WORK_ROOT" node "$CREATE" --icon "$ICON" --brand brand.css --out icon-out.svg
  OUTPUT="$(cat "$WORK_ROOT/icon-out.svg")"
  expect "Icon zentriert mit Strich-Attributen" 0 \
    '<g transform="translate(4 4)" fill="none" stroke="currentColor" stroke-width="2">'
  expect "Icon-Pfad übernommen" 0 '<path d="M12 5v14" />'
  write_icon "0 0 32 32"
  run_in "$WORK_ROOT" node "$CREATE" --icon "$ICON" --brand brand.css --out bad.svg
  expect "fremde viewBox abgelehnt" 1 'erwartet ein Tabler-Icon mit viewBox="0 0 24 24"'
}

test_usage
test_default_output_uses_kit_brand
test_brand_colors_per_mode
test_text_is_escaped
test_rejects_long_text
test_rejects_non_oklch
test_icon
finish
