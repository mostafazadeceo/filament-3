#!/usr/bin/env bash
set -euo pipefail

if [[ "${APP_ENV:-}" == "production" ]]; then
  echo "Refusing to run QA sanity in production." >&2
  exit 1
fi

if [[ -x "./vendor/bin/pint" ]]; then
  ./vendor/bin/pint --test
fi

DB_CONNECTION=sqlite DB_DATABASE=/tmp/haida_qa.sqlite CACHE_STORE=array QUEUE_CONNECTION=sync php artisan test
DB_CONNECTION=sqlite DB_DATABASE=/tmp/haida_demo.sqlite CACHE_STORE=array QUEUE_CONNECTION=sync ./scripts/demo-e2e.sh
DB_CONNECTION=sqlite DB_DATABASE=/tmp/haida_deep.sqlite CACHE_STORE=array QUEUE_CONNECTION=sync php scripts/deep_scenario_runner.php
