# Loyalty Club Installation

## Prerequisites
- PHP 8.4+
- Laravel 12+
- Filament v4
- Filamat IAM Suite (tenancy + permissions)

## Install (monorepo)
1) Add path repository in root `composer.json`:
```
{
  "type": "path",
  "url": "packages/filament-loyalty-club",
  "options": {"symlink": true}
}
```
2) Require the package:
```
composer require haida/filament-loyalty-club
```
3) Run migrations:
```
php artisan migrate --force
```

## Enable in panels
Register the plugin in both panels:
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/TenantPanelProvider.php`
```
FilamentLoyaltyClubPlugin::make()
```

## Config
Config file: `config/filament-loyalty-club.php`
- Points expiry strategy, caps, and retention
- Cashback enablement + adapter selection
- Referral fraud settings
- RFM thresholds
- Campaign notify panel/event

Notes:
- `LOYALTY_POINTS_EXPIRY_STRATEGY=fixed|inactivity` (inactivity extends bucket expiries on activity).

## Adapters
- Orders: `PurchaseAdapterInterface`
- Wallet: `WalletAdapterInterface`

Default adapters are provided (CommerceOrders + internal wallet). Override via container binding if needed.

## Scheduled jobs (recommended)
- Expire points: call `LoyaltyExpiryService::expirePoints()` daily.
- Expiry notifications: call `LoyaltyExpiryService::notifyUpcomingExpiries()` daily.
- Referral payouts: call `LoyaltyReferralService::processDueRewards()` to settle waiting-period rewards.
- Retention prune: run `php artisan loyalty:retention:prune` weekly or monthly.
