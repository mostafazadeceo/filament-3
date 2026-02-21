# Filament MailOps — API

Base path: `/api/v1/filament-mailops`

## احراز و مجوز
Middleware: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope`.

## Endpointها
- `GET /domains` — لیست دامنه‌ها (`mailops.domain.view`)
- `POST /domains` — ایجاد دامنه (`mailops.domain.manage`)
- `GET /domains/{id}` — مشاهده دامنه (`mailops.domain.view`)
- `PATCH /domains/{id}` — ویرایش دامنه (`mailops.domain.manage`)
- `DELETE /domains/{id}` — حذف دامنه (`mailops.domain.manage`)

### Domain validation (v2)
- `name`: دامنه معتبر (FQDN)، یکتا در سطح tenant.
- `status`: یکی از `active|inactive|pending|failed`.
- `dkim_selector`: حداکثر 63 کاراکتر (`a-z0-9._-`).
- `dkim_public_key`: حداکثر 12000 کاراکتر.
- `comment`: حداکثر 5000 کاراکتر.

### Domain computed fields
- `dns_health_status`: `healthy|warning|critical|unknown`
- `dns_health_score`: امتیاز 0..100
- `dns_last_checked_at`: زمان آخرین ممیزی DNS
- `dns_issues`: لیست خطاهای مهم DNS

- `GET /mailboxes` — لیست صندوق‌ها (`mailops.mailbox.view`)
- `POST /mailboxes` — ایجاد صندوق (`mailops.mailbox.manage`)
- `GET /mailboxes/{id}` — مشاهده صندوق (`mailops.mailbox.view`)
- `PATCH /mailboxes/{id}` — ویرایش صندوق (`mailops.mailbox.manage`)
- `DELETE /mailboxes/{id}` — حذف صندوق (`mailops.mailbox.manage`)

- `GET /aliases` — لیست نام‌های مستعار (`mailops.alias.view`)
- `POST /aliases` — ایجاد نام مستعار (`mailops.alias.manage`)
- `GET /aliases/{id}` — مشاهده (`mailops.alias.view`)
- `PATCH /aliases/{id}` — ویرایش (`mailops.alias.manage`)
- `DELETE /aliases/{id}` — حذف (`mailops.alias.manage`)

- `GET /outbound-messages` — لیست ارسال‌ها (`mailops.outbound.view`)
- `POST /outbound-messages` — ارسال ایمیل (`mailops.outbound.send`)
- `GET /outbound-messages/{id}` — مشاهده ارسال (`mailops.outbound.view`)

- `GET /inbound-messages` — لیست دریافتی‌ها (`mailops.inbound.view`)
- `GET /inbound-messages/{id}` — مشاهده پیام (`mailops.inbound.view`)
- `POST /inbound-messages/sync` — همگام‌سازی IMAP (`mailops.inbound.sync`)

- `GET /openapi` — دریافت سند OpenAPI
