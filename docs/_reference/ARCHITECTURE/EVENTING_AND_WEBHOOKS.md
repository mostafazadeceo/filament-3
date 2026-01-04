# EVENTING_AND_WEBHOOKS

این سند سیاست رویدادها، وبهوک‌ها، idempotency و retry را مشخص می‌کند.

## کاتالوگ رویدادها (خلاصه)
- IAM Suite: وبهوک‌های اتوماسیون و اعلان‌ها (`Webhook`, `WebhookDelivery`, `DeliverWebhookJob`).
- Payments / Orchestrator: دریافت وبهوک ارائه‌دهندگان پرداخت و ثبت رویداد (`PaymentWebhookEvent`).
- Crypto Gateway/Nodes: ingest و پردازش وبهوک‌ها با adapterهای اختصاصی.
- Relograde: دریافت وبهوک و پردازش async با `ProcessWebhookEventJob`.
- Providers eSIM Go: مسیر webhook اختصاصی برای همگام‌سازی وضعیت‌ها.

## Idempotency
- کلید یکتا (idempotency key) برای جلوگیری از پردازش تکراری.
- نمونه‌ها: وبهوک پرداخت/crypto، عملیات POS، اتوماسیون IAM.
- [ASSUMPTION] برای همه عملیات حساس (مالی/ledger) idempotency key الزامی است.

## Retry و DLQ
- پردازش وبهوک‌ها از طریق Job انجام می‌شود و retry توسط queue کنترل می‌شود.
- [ASSUMPTION] DLQ به‌صورت صف جداگانه یا مانیتورینگ شکست‌ها پیاده‌سازی شود.

## امنیت وبهوک
- امضای HMAC و محدودسازی IP برای برخی ارائه‌دهندگان.
- جلوگیری از replay با nonce و window زمانی.

## observability
- مانیتورینگ backlog و نرخ شکست وبهوک‌ها.
- Runbook باید سناریوهای `Webhook backlog` و `Queue stuck` را پوشش دهد.
