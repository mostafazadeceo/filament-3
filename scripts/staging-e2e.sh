#!/usr/bin/env bash
set -euo pipefail

if [[ "${APP_ENV:-}" == "production" ]]; then
  echo "Refusing to run staging-e2e in production." >&2
  exit 1
fi

DB_CONNECTION="${DB_CONNECTION:-sqlite}"

if [[ -n "${DATABASE_URL:-}" ]]; then
  export DATABASE_URL
elif [[ "${DB_CONNECTION}" == "sqlite" ]]; then
  SQLITE_PATH="${SQLITE_PATH:-/tmp/haida_staging.sqlite}"
  export DB_CONNECTION=sqlite
  export DB_DATABASE="${SQLITE_PATH}"
fi

export CACHE_STORE="${CACHE_STORE:-array}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

if [[ "${DB_CONNECTION}" != "sqlite" && -z "${DATABASE_URL:-}" ]]; then
  echo "Non-sqlite requires DATABASE_URL." >&2
  exit 1
fi

if [[ "${DB_CONNECTION}" != "sqlite" && "${STAGING_ALLOW_MIGRATE:-0}" != "1" ]]; then
  echo "Set STAGING_ALLOW_MIGRATE=1 to run against non-sqlite staging." >&2
  exit 1
fi

php artisan migrate --force

php artisan test

DB_CONNECTION="${DB_CONNECTION}" DB_DATABASE="${DB_DATABASE:-}" CACHE_STORE="${CACHE_STORE}" QUEUE_CONNECTION="${QUEUE_CONNECTION}" ./scripts/demo-e2e.sh
DB_CONNECTION="${DB_CONNECTION}" DB_DATABASE="${DB_DATABASE:-}" CACHE_STORE="${CACHE_STORE}" QUEUE_CONNECTION="${QUEUE_CONNECTION}" php scripts/deep_scenario_runner.php
