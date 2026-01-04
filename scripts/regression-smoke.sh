#!/usr/bin/env bash
set -euo pipefail

if [[ "${APP_ENV:-}" == "production" ]]; then
  echo "Refusing to run regression smoke in production." >&2
  exit 1
fi

export DB_CONNECTION=sqlite
export DB_DATABASE="${DB_DATABASE:-/tmp/haida_regression.sqlite}"
export CACHE_STORE="${CACHE_STORE:-array}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

php artisan test
DB_CONNECTION=sqlite DB_DATABASE=/tmp/haida_demo.sqlite CACHE_STORE=array QUEUE_CONNECTION=sync ./scripts/demo-e2e.sh
DB_CONNECTION=sqlite DB_DATABASE=/tmp/haida_deep.sqlite CACHE_STORE=array QUEUE_CONNECTION=sync php scripts/deep_scenario_runner.php
