# Filament MailOps — INSTALL

## نصب پکیج
1) composer را به‌روز کنید:
```bash
composer update haida/filament-mailops
```
2) مایگریشن‌ها را اجرا کنید:
```bash
php artisan migrate --force
```
این مرحله شامل ستون‌های ممیزی DNS برای `mailops_domains` نیز هست.

## تنظیمات اصلی (.env)
```dotenv
MAILOPS_SMTP_HOST=mail.example.com
MAILOPS_SMTP_PORT=587
MAILOPS_SMTP_ENCRYPTION=tls

MAILOPS_IMAP_HOST=mail.example.com
MAILOPS_IMAP_PORT=993
MAILOPS_IMAP_ENCRYPTION=ssl
MAILOPS_IMAP_VERIFY_TLS=true

MAILOPS_INBOUND_SYNC_LIMIT=50
MAILOPS_INBOUND_STORE_BODY=true
MAILOPS_OUTBOUND_STORE_BODY=true
```

## پیش‌نیاز دریافت ایمیل
- افزونه `imap` برای PHP باید نصب باشد.

## Mailu API (اختیاری)
```dotenv
MAILOPS_MAILU_ENABLED=false
MAILOPS_MAILU_BASE_URL=https://mail.example.com/api/v1
MAILOPS_MAILU_TOKEN=your_mailu_token
MAILOPS_MAILU_VERIFY_TLS=true
```

## تنظیمات اختصاصی صندوق
- اگر مقادیر SMTP/IMAP را در فرم صندوق وارد نکنید، تنظیمات سراسری استفاده می‌شود.

## ممیزی DNS دامنه‌ها
- پس از ساخت/ویرایش دامنه، از اکشن `ممیزی DNS` برای محاسبه امتیاز سلامت استفاده کنید.
- اگر Mailu فعال باشد، اکشن `به‌روزرسانی Snapshot DNS` مقدار رکوردها را از Mailu می‌خواند و ممیزی را به‌روز می‌کند.

## پنل‌ها
- پلاگین در پنل‌های Admin و Tenant ثبت شده است.
