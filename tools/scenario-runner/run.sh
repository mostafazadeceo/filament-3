#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

php "$ROOT_DIR/scripts/deep_scenario_runner.php"

printf "Scenario runner completed.\n"
