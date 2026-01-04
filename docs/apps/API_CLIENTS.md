# API Clients

## مسیرهای OpenAPI
- App API: `/api/v1/app/openapi`
- POS: `/api/v1/filament-pos/openapi`
- Workhub: `/api/v1/workhub/openapi`
- Meetings: `/api/v1/meetings/openapi`
- Attendance: `/api/v1/payroll-attendance/openapi`
- Crypto: `/api/v1/crypto/openapi`
- Loyalty: `/api/v1/loyalty/openapi`

## تولید TypeScript Client
```
./tools/openapi/generate-ts-client.sh
```
خروجی در `apps/web-pwa/src/lib/api-client/`.

## تولید Kotlin Client
```
./tools/openapi/generate-kotlin-client.sh
```
خروجی در `apps/mobile-android/app/src/main/java/com/haida/hubapp/api/`.

## نکات
- برای اجرا نیاز به `openapi-generator-cli` یا فایل jar دارید.
- اگر از Docker استفاده می‌کنید، به شبکه و دسترسی Registry نیاز است.
- متغیر `OPENAPI_URL` مسیر دریافت spec را تغییر می‌دهد.
