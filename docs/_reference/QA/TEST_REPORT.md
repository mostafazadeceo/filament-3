# TEST_REPORT

## آخرین اجرا
- تاریخ: 2026-01-03T22:30:21+03:30
- وضعیت کلی: PASS

## دستورات اجرا شده
- `php artisan test`
- `php scripts/deep_scenario_runner.php`
- `./vendor/bin/pint packages/filament-payments packages/filament-currency-rates`
- `./vendor/bin/pint --test packages/filament-payments packages/filament-currency-rates`
- `./vendor/bin/pint packages/filament-petty-cash-ir`
- `./vendor/bin/pint --test packages/filament-restaurant-ops packages/filament-petty-cash-ir packages/filament-payroll-attendance-ir`
- `npm run build` (apps/web-pwa)
- `npm run test` (apps/web-pwa)
- `npm run test:e2e` (apps/web-pwa)
- `./gradlew assembleDebug` (apps/mobile-android)
- `./gradlew testDebugUnitTest` (apps/mobile-android)
- `ANDROID_SDK_ROOT=/opt/android-sdk ./gradlew pixel2api30DebugAndroidTest` (apps/mobile-android)

## نتایج
### php artisan test
- نتیجه: PASS
- Tests: 118 passed (314 assertions)

### deep_scenario_runner
- نتیجه: PASS

### Pint (ماژول‌های تغییرکرده)
- نتیجه: PASS (پس از فرمت در filament-payments و filament-currency-rates)

### Pint (restaurant-ops, petty-cash, payroll-attendance)
- نتیجه: PASS (پس از اجرای pint در filament-petty-cash-ir)

### Pint (سراسری)
- اجرا نشد (خارج از محدوده تغییرات و منع فرمت سراسری طبق AGENTS).

### PHPStan/Larastan
- وضعیت: اجرا نشد (ابزار نصب نبود)

### Web/PWA build
- نتیجه: PASS
- هشدار: ندارد (warning قبلی درباره outputFileTracingRoot رفع شد)

### Web/PWA unit tests (vitest)
- نتیجه: PASS (2 files, 5 tests)

### Web/PWA e2e (playwright)
- نتیجه: PASS (1 test)

### Android assembleDebug
- نتیجه: PASS

### Android testDebugUnitTest
- نتیجه: PASS

### Android pixel2api30DebugAndroidTest (managed device)
- نتیجه: PASS

### Android connectedDebugAndroidTest
- نتیجه: اجرا نشد (managed device استفاده شد)

## جمع‌بندی
- Backend tests و deep scenario runner موفق بودند.
- Pint برای payments/currency-rates و filament-petty-cash-ir پاس شد.
- Web/PWA build و تست‌ها سبز شدند و هشدار lockfile رفع شد.
- Android managed device tests سبز شدند؛ connectedDebugAndroidTest اجرا نشد.
