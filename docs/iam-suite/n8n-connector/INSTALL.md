# N8N Intelligence Connector نصب و راه‌اندازی

## پیش‌نیازها
- PHP 8.2+, Laravel 11.28+, Filament v4
- فعال بودن IAM Suite در پروژه

## نصب (Package-first)
این اتصال بخشی از پکیج `filamat/filamat-iam-suite` است و نیاز به نصب جداگانه ندارد.

## پیکربندی
نمونه تنظیمات در `config/filamat-iam.php`:
- `automation.enabled` (default: true)
- `automation.webhook_type` (default: automation)
- `automation.default_auth_mode` (hmac+nonce | header | basic | jwt | none)
- `automation.event_catalog` (config key for event catalog)
- `automation.inbound.auth_mode` (header | hmac+nonce)
- `automation.inbound.token_header` / `automation.inbound.token`
- `automation.n8n_api.enabled` + `automation.n8n_api.base_url` + `automation.n8n_api.api_key`
- `automation.redaction_defaults` (حذف/ماسک PII)
- `automation.action_proposals.enabled`
- `automation.schedule.enabled` + `automation.schedule.audit_time` + `automation.schedule.prune_time`

### متغیرهای محیطی پیشنهادی
```
FILAMAT_IAM_N8N_INBOUND_TOKEN=change-me
FILAMAT_IAM_N8N_API_KEY=
FILAMAT_IAM_N8N_API_BASE_URL=
```

## ساخت کانکشن n8n (Outbound)
1. در Filament → وبهوک‌ها، نوع را روی «اتوماسیون (n8n)» بگذارید.
2. رویدادهای مدنظر را انتخاب کنید (اگر خالی باشد، همه رویدادها ارسال می‌شوند).
3. URL وبهوک n8n را وارد کنید.
4. کلید امضا خودکار ایجاد می‌شود (HMAC + nonce).

## n8n Workflow (Webhook Trigger)
- یک Webhook Trigger بسازید.
- در بخش Authentication پیشنهاد می‌شود از Header Auth استفاده شود.
- مقدار امضای دریافتی را در workflow بررسی کنید (اختیاری، اما توصیه‌شده).

## Inbound Callback (گزارش/پیشنهاد)
- Endpoint: `POST /api/v1/iam/n8n/callback`
- Header Auth: `X-Api-Key` + `X-Tenant-ID` + توکن ثابت (یا امضای HMAC)
- بدنه باید شامل `idempotency_key` و `report` یا `proposal` باشد.

## زمان‌بندی (Scheduler)
- `iam:ai-audit:run` و `iam:automation:prune` به‌صورت پیش‌فرض روزانه اجرا می‌شوند.
- زمان‌بندی از طریق `automation.schedule.*` قابل تنظیم است.

## نکات امنیتی
- PII به‌صورت پیش‌فرض حداقلی ارسال می‌شود.
- امضای HMAC و nonce برای جلوگیری از replay فعال است.
- نرخ ارسال per-tenant محدود می‌شود.
