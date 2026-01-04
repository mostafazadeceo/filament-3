# Upgrade Notes

## v1.0.0
- Added loyalty core tables (customers, tiers, rules, events).
- Added wallet/ledger tables with points buckets and consumptions.
- Added rewards/coupons, referrals, missions/badges, segments/campaigns, audit/fraud, metrics.
- Added donation pledges for charity redemptions.
- New config: `config/filament-loyalty-club.php`.
- New permissions under `loyalty.*` (see capability registry).

## Migrations
Run:
```
php artisan migrate --force
```

## Permissions
Assign the new permissions to roles as needed:
- `loyalty.customer.*`, `loyalty.tier.*`, `loyalty.rule.*`
- `loyalty.reward.*`, `loyalty.coupon.*`
- `loyalty.referral.*`, `loyalty.mission.*`, `loyalty.badge.*`
- `loyalty.segment.*`, `loyalty.campaign.*`
- `loyalty.fraud.*`, `loyalty.audit.view`, `loyalty.event.ingest`
- `loyalty.settings.manage`, `loyalty.ai.*`
