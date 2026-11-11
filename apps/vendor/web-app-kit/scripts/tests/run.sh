#!/usr/bin/env bash
set -euo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
failed=0
for test_file in "$TESTS_DIR"/*.test.sh; do
  echo "== $(basename "$test_file")"
  bash "$test_file" || failed=1
done
exit "$failed"
