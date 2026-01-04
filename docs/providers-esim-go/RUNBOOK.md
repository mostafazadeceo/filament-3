# RUNBOOK — eSIM Go

## تنظیم اتصال
1) در پنل: بخش «Provider eSIM Go» → «اتصال» → ایجاد اتصال
2) API Key را وارد کنید.
3) Test Connection را اجرا کنید.

## Callback / Webhook
- آدرس پیشنهادی: `/api/v1/providers/esim-go/callback?connection_id=<id>`
- HMAC روی raw-body با key=API Key
- Header امضا قابل تنظیم است (پیش‌فرض لیست هدرها در config)

## V2/V3
- هر دو نسخه پشتیبانی می‌شود.
- اگر payload شامل ساختار V3 باشد، parser آن استفاده می‌شود.

## Rate limit و Retry
- 10 TPS داخلی؛ درخواست‌ها صف‌بندی می‌شوند.
- 503 با Retry-After رعایت می‌شود.

## No-Surveillance
- رویدادهای location فقط ACK می‌شوند و ذخیره نمی‌گردند.

## اعلان‌ها
- وبهوک‌های معتبر یک Trigger با نام `webhook_received` روی مدل Callback ایجاد می‌کنند.
- پنل هدف از طریق `ESIM_GO_NOTIFY_PANEL` قابل تنظیم است (پیش‌فرض: tenant).
