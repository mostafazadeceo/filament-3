# Security & Privacy

## Threat model خلاصه
- افشای توکن‌ها و نشست‌ها
- سوءاستفاده از push payload
- نشت داده‌های آفلاین
- اجرای درخواست‌های تکراری (idempotency)
- حمله MITM

## ذخیره‌سازی امن
- Android: EncryptedSharedPreferences + Keystore-backed.
- Web: ترجیح HttpOnly cookies، در صورت استفاده از IndexedDB، داده حساس با encryption ذخیره شود.

## Biometric
- BiometricPrompt برای unlock session و عملیات حساس (پرداخت/تسویه/تنظیمات).

## Integrity
- Play Integrity token با feature-flag (stub فعلی)؛ در login/refresh ارسال می‌شود.

## Network hardening
- TLS اجباری، timeout و retry policy تعریف شده.
- Certificate pinning به‌صورت feature-flag قابل افزودن است.

## Privacy by Design
- Location/face opt-in و permission-based.
- Push بدون محتوای حساس (فقط اطلاع از به‌روزرسانی).
- Masking برای داده‌های حساس در گزارش‌ها.

## Logging
- هیچ secret در لاگ ثبت نمی‌شود.
- خطاها با metadata محدود گزارش می‌شوند.
