# DEBUG_REPORT

Last updated: 2026-01-03T22:30:21+03:30

## Scope
- Backend: Laravel + Filament + packages/*
- Web/PWA: apps/web-pwa
- Android: apps/mobile-android

## Plan (M1-M9)
1. Deep scan + baseline mapping + issue taxonomy. (done)
2. Build/test baseline (backend + web + android) with logs. (done)
3. Tenant/IAM leak hunt + tests. (done)
4. Webhook/payment/idempotency audit + tests. (done)
5. Performance/N+1/index budget review. (done)
6. Security review (ASVS/MASVS). (done)
7. Web/PWA offline + realtime + sync validation. (done)
8. Android offline + WorkManager + crash validation. (done)
9. Hardening + final reports. (done)

## Executive Summary (Top Issues)
1. DBG-005: Payment webhooks tenant collision fixed with tenant-scoped uniqueness and context. (fixed)
2. DBG-006: PWA outbox no longer stuck in syncing on failures. (fixed)
3. DBG-007: Currency rates API now requires token + throttling. (fixed)
4. DBG-003: Android FCM token registration implemented. (fixed)
5. DBG-004: Android Play Integrity token integrated behind feature flag. (fixed)
6. DBG-010: Android sync pull now persists changes locally. (fixed)
7. DBG-012: Next.js workspace warning removed via outputFileTracingRoot. (fixed)
8. DBG-011: OpenAPI endpoints now require module scopes + coverage test. (fixed)
9. DBG-009: Managed device Android tests now pass under ANDROID_SDK_ROOT. (fixed)
10. DBG-008: Pint style issues in filament-petty-cash-ir resolved via package lint pass. (fixed)

## Issue Table
| ID | Severity | Area | Repro Steps | Root Cause | Fix | Tests | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| DBG-002 | low | web | Force WS disconnects | Fallback handler was TODO | Trigger sync on fallback | npm run test | fixed |
| DBG-003 | medium | android | Rotate FCM token | Token registration missing | Add DeviceRepository + FCM upload | ./gradlew assembleDebug | fixed |
| DBG-004 | medium | android | Request integrity token | Play Integrity stub | Implement Play Integrity behind flag | ./gradlew assembleDebug | fixed |
| DBG-005 | high | backend | Process same webhook external_id on 2 tenants | Unique index lacked tenant + no context | Add tenant-scoped unique index + set TenantContext | php artisan test | fixed |
| DBG-006 | medium | web | Simulate push failure | Outbox items left syncing | Mark failed on error + handle missing results | npm run test | fixed |
| DBG-007 | high | backend | Call /currency-rates without token | tokenValid allowed blank token | Require token + add rate limiting | php artisan test | fixed |
| DBG-008 | low | infra | Run pint --test on touched packages | Existing style issues in filament-petty-cash-ir | Run pint on filament-petty-cash-ir and re-check | ./vendor/bin/pint --test packages/filament-restaurant-ops packages/filament-petty-cash-ir packages/filament-payroll-attendance-ir | fixed |
| DBG-009 | medium | android | Run managed device tests | ANDROID_SDK_ROOT missing in prior run | Run managed device tests with ANDROID_SDK_ROOT set | ANDROID_SDK_ROOT=/opt/android-sdk ./gradlew pixel2api30DebugAndroidTest | fixed |
| DBG-010 | medium | android | Pull sync changes | Changes not persisted locally | Add sync change store + applyChanges | ./gradlew assembleDebug | fixed |
| DBG-011 | low | api | Request openapi without view scope | OpenAPI routes missing scope middleware in 3 modules | Add scope middleware + coverage test | php artisan test | fixed |
| DBG-012 | low | web | Build web-pwa | Next.js lockfile warning | Set outputFileTracingRoot | npm run build | fixed |

## Tenant/IAM Risk Review
- Tenant scoping verified via scenario runner across three tenants; no leaks observed post-fix.
- Payment webhooks now tenant-scoped at handler and DB unique index layer.
- Android client now sends X-Tenant-ID and X-Device-ID headers to support tenant resolution.
- OpenAPI endpoints now require module view scopes (restaurant-ops, petty-cash, payroll-attendance).

## Performance Review
- Added tenant-scoped unique index for payment webhooks; no new N+1 issues detected in scan.
- No runtime query profiling beyond tests.

## Security Review
- ASVS: currency-rates API now requires token + throttling; payment webhook tenant scoping tightened.
- MASVS: Play Integrity token integrated behind feature flag; FCM token registration implemented.
- ASVS: OpenAPI endpoints now gated by module view scopes with coverage test.

## Files Changed
- packages/filament-payments/src/Services/WebhookHandler.php (set tenant context during webhook handling)
- packages/filament-payments/database/migrations/2026_01_03_000003_update_payment_webhook_unique_index.php (tenant-scoped webhook unique index)
- packages/filament-payments/src/FilamentPaymentsServiceProvider.php (register new migration)
- tests/Feature/Payments/PaymentWebhookSecurityTest.php (tenant uniqueness coverage)
- packages/filament-currency-rates/config/currency-rates.php (rate limit config)
- packages/filament-currency-rates/routes/api.php (api middleware + throttle)
- packages/filament-currency-rates/src/Http/Controllers/CurrencyRateApiController.php (require API token)
- apps/web-pwa/src/lib/sync/sync-engine.ts (outbox error handling)
- apps/web-pwa/src/lib/sync/sync-engine.test.ts (sync outbox tests)
- apps/web-pwa/src/lib/realtime/use-realtime.ts (fallback sync)
- apps/web-pwa/next.config.mjs (outputFileTracingRoot)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/remote/AppApiService.kt (device + integrity payloads)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/repository/AuthRepository.kt (Play Integrity token on login)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/repository/DeviceRepository.kt (device + token registration)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/repository/SyncRepository.kt (apply pull changes + outbox error handling)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/local/SyncChangeEntity.kt (sync change store)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/local/SyncChangeDao.kt (sync change DAO)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/local/Migrations.kt (Room migration)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/data/local/AppDatabase.kt (version bump + exportSchema false)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/notifications/HubMessagingService.kt (FCM token upload)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/MainActivity.kt (device registration on start)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/security/SecureStore.kt (device/token storage)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/security/SecureStoreImpl.kt (device/token storage)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/security/PlayIntegrityManager.kt (Play Integrity integration)
- apps/mobile-android/app/build.gradle.kts (Play Integrity build config)
- apps/mobile-android/app/src/main/java/com/haida/hubapp/di/AppModule.kt (headers + migration + DAO)
- packages/filament-restaurant-ops/routes/api.php (openapi scope gate)
- packages/filament-petty-cash-ir/routes/api.php (openapi scope gate)
- packages/filament-petty-cash-ir/* (pint formatting pass)
- packages/filament-payroll-attendance-ir/routes/api.php (openapi scope gate)
- tests/Feature/OpenApiScopeCoverageTest.php (openapi middleware coverage)
- docs/_reference/QA/DEBUG_REPORT.md (issue/status updates)
- docs/_reference/QA/DEBUG_REPORT.json (issue/status updates)
- docs/_reference/QA/TEST_REPORT.md (latest test results)
- docs/_reference/ARCHITECTURE/SECURITY_MODEL.md (security updates)
- docs/_reference/CHANGELOG.md (behavior changes)
