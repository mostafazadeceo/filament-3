# IAM Suite Install / Upgrade

## Prereqs
- PHP 8.2+ / Laravel 11.28+ / Filament v4
- `filamat-iam-suite` package installed via path repository
- `spatie/laravel-permission` with teams enabled (`tenant_id`)
- Queue worker for notifications and capability sync

## Migration
- Run migrations (production: `php artisan migrate --force`).
- Ensure session driver is `database` for session revoke support.

## Config
- `config/filamat-iam.php`:
  - Enable PAM, sessions, protected actions, MFA.
  - Configure impersonation defaults (TTL, reason/ticket requirements).
  - Configure protected action TTL and MFA-required actions.

## Scheduled Jobs (Recommended)
- Expire PAM activations and requests (hourly).
- Send PAM weekly digest: `php artisan iam:pam:digest` (weekly).
- Cleanup old sessions (daily, retention days).

## Permissions
- Sync capability registry:
  - `php artisan filamat-iam:sync-capabilities`

## Notifications
- Ensure `haida/filament-notify-core` channels are configured.

## Verification
- Run tests: `php artisan test`.
- Scenario runner: `php scripts/deep_scenario_runner.php`.
