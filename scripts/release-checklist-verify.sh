#!/usr/bin/env bash
set -euo pipefail

files=(
  "docs/00-assumptions.md"
  "docs/01-audit.md"
  "docs/02-dependency-graph.mmd"
  "docs/03-architecture.md"
  "docs/04-erd.mmd"
  "docs/05-workflows.mmd"
  "docs/06-ui-relograde-v1.md"
  "docs/07-plugin-lifecycle.md"
  "docs/08-test-matrix.md"
  "docs/09-release-plan.md"
  "docs/99-release-checklist.md"
  "docs/delivery-notes.md"
  "docs/staging-runbook.md"
  "docs/qa-checklist.md"
  "docs/regression-audit.md"
  "scripts/demo-e2e.sh"
  "scripts/deep_scenario_runner.php"
  "scripts/qa-sanity.sh"
  "scripts/regression-smoke.sh"
  "scripts/staging-e2e.sh"
)

missing=0
for file in "${files[@]}"; do
  if [[ ! -f "$file" ]]; then
    echo "Missing: $file" >&2
    missing=1
  fi
done

if [[ $missing -ne 0 ]]; then
  exit 1
fi

echo "Release checklist artifacts verified."
