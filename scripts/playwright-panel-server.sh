#!/usr/bin/env bash
set -euo pipefail

if [[ "${APP_ENV:-}" == "production" ]]; then
  echo "Refusing to run browser E2E server in production." >&2
  exit 1
fi

HOST="${PLAYWRIGHT_PANEL_HOST:-127.0.0.1}"
PORT="${PLAYWRIGHT_PANEL_PORT:-8099}"

export APP_ENV="${APP_ENV:-testing}"
export APP_URL="${APP_URL:-http://${HOST}:${PORT}}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export CACHE_STORE="${CACHE_STORE:-array}"
export CACHE_DRIVER="${CACHE_DRIVER:-array}"
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
export LIVEWIRE_ASSET_URL="${LIVEWIRE_ASSET_URL:-/vendor/livewire/livewire.min.js}"

if [[ "${DB_CONNECTION}" == "sqlite" ]]; then
  export DB_DATABASE="${DB_DATABASE:-/tmp/filament3_playwright.sqlite}"
  if [[ "${PLAYWRIGHT_PANEL_RESET_DB:-0}" == "1" ]]; then
    rm -f "${DB_DATABASE}"
  fi
  mkdir -p "$(dirname "${DB_DATABASE}")"
  touch "${DB_DATABASE}"
fi

php artisan optimize:clear >/dev/null 2>&1 || true
php artisan migrate --force

php artisan tinker --execute "
use App\Models\User;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;

\$email = 'e2e-mega@example.test';
\$password = 'E2E#Secret1234';
\$tenantSlug = 'e2e-tenant';

\$user = User::firstOrCreate(
    ['email' => \$email],
    ['name' => 'E2E Mega', 'password' => bcrypt(\$password)]
);
\$user->name = 'E2E Mega';
\$user->password = bcrypt(\$password);
\$user->is_super_admin = true;
\$user->email_verified_at = \$user->email_verified_at ?: now();
\$user->save();

\$org = Organization::firstOrCreate(
    ['name' => 'E2E Org'],
    ['owner_user_id' => \$user->getKey()]
);

\$tenant = Tenant::firstOrCreate(
    ['slug' => \$tenantSlug],
    [
        'name' => 'E2E Tenant',
        'organization_id' => \$org->getKey(),
        'owner_user_id' => \$user->getKey(),
        'status' => 'active',
    ]
);

\$user->tenants()->syncWithoutDetaching([
    \$tenant->getKey() => [
        'role' => 'owner',
        'status' => 'active',
        'joined_at' => now(),
    ],
]);
"

exec php artisan serve --host="${HOST}" --port="${PORT}"
