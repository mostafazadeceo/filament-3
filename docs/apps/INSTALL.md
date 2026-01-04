# نصب و اجرای کلاینت‌ها

## پیش‌نیازها
- Node.js 20+
- pnpm یا npm
- JDK 17
- Android Studio / Gradle
- PHP 8.2 + Composer (برای OpenAPI و سناریوها)

## متغیرهای محیطی
### Web/PWA (`apps/web-pwa/.env.local`)
```
NEXT_PUBLIC_API_BASE_URL=http://localhost
NEXT_PUBLIC_API_KEY=<api-key>
```

### Android (`apps/mobile-android/gradle.properties` یا متغیر محیطی)
```
HUB_API_BASE_URL=http://10.0.2.2
HUB_API_KEY=<api-key>
```

## اجرای Web/PWA
```
cd apps/web-pwa
npm install
npm run dev
```

## ساخت Web/PWA
```
cd apps/web-pwa
npm run build
```

## اجرای Android
```
cd apps/mobile-android
echo "sdk.dir=/path/to/android-sdk" > local.properties
./gradlew assembleDebug
```

## ساخت Android Release
```
cd apps/mobile-android
./gradlew assembleRelease
```

## تولید OpenAPI Clients
```
tools/openapi/generate-ts-client.sh
tools/openapi/generate-kotlin-client.sh
```

## سناریوهای E2E
```
./tools/scenario-runner/run.sh
```

## اسکریپت‌های Build/Test
```
./tools/build/build-web.sh
./tools/build/test-web.sh
./tools/build/build-android.sh
./tools/build/test-android.sh
```

## چک‌لیست انتشار
- PWA: build و deploy روی HTTPS با تنظیم cache headers.
- Android: keystore + signing config در `gradle.properties`.
- TURN/STUN: مقداردهی `APP_TURN_SERVERS`.
- Push: تنظیم FCM (Android) و Web Push/FCM (Web) در محیط تولید.

## نکات
- برای push web لازم است HTTPS و VAPID/FCM فعال باشد.
- TURN/STUN در `APP_TURN_SERVERS` تنظیم شود.
- مهاجرت‌ها در پروداکشن: `php artisan migrate --force`.
