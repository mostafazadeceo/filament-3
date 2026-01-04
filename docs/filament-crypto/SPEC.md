# SPEC — پلتفرم کیف‌پول و درگاه رمزارز (Filament v4)

## شمال ستاره
ساخت یک پلتفرم جهانی «کیف‌پول و پرداخت رمزارز» مشابه Cryptomus/CoinPayments/Coinbase Commerce با قابلیت self-hosted (BTCPay) برای SaaS چندمستاجری، با امنیت بالا، دفترکل دوطرفه، و تجربه فارسی.

## اهداف کلیدی
- ایجاد Invoice/Charge با idempotency بر اساس order_id.
- پشتیبانی از پرداخت‌های کم/زیاد/جزئی + تایید آنچین.
- وبهوک امن، سریع، و قابل replay با DLQ.
- برداشت/تسویه با workflow تایید و whitelist مقصد.
- آشتی‌سازی (reconcile) برای پوشش وبهوک‌های دیررس.
- Ledger دوطرفه برای مانده‌ها و گزارش درآمد/کارمزد.
- پلن/کارمزد برای محصول مستقل.
- AI Auditor صرفاً مالی (بدون نظارت انسانی).

## غیرهدف‌ها
- هر نوع نظارت/ردیابی افراد یا پروفایلینگ کارکنان.
- ذخیره ویدئو/لوگ‌های غیرضروری.

## معماری پکیج‌ها
1) `packages/filament-crypto-core`
   - مدل‌های دامنه (Wallet, Address, Ledger, Rates, Network Fees)
   - سرویس Ledger دوطرفه + Policy fee/plan
   - Audit log مالی + Event bus داخلی
2) `packages/filament-crypto-gateway`
   - Provider adapters (Cryptomus/CoinPayments/Coinbase)
   - Invoice/Payout orchestration
   - Webhook framework + idempotency + replay + DLQ
   - API /api/v1/crypto + OpenAPI
3) `packages/filament-crypto-nodes`
   - Self-hosted fallback (BTCPay) + کانکتورهای نود
   - Health checks نود/کانکتور
   - Bitcoin Core RPC: getnewaddress/getreceivedbyaddress/gettransaction
   - EVM JSON-RPC: eth_newBlockFilter/eth_getFilterChanges/eth_getLogs

## مدل داده (خلاصه)
### crypto-core
- crypto_accounts, crypto_ledgers, crypto_ledger_entries
- crypto_wallets, crypto_addresses
- crypto_rates, crypto_network_fees
- crypto_audit_events (لاگ مالی)
- crypto_fee_policies (سیاست کارمزد/پلن)

### crypto-gateway
- crypto_provider_accounts
- crypto_invoices, crypto_invoice_payments
- crypto_payouts
- crypto_payout_destinations (whitelist)
- crypto_webhook_calls
- crypto_reconciliations
- crypto_ai_reports

## State Machine داخلی
`draft | unpaid | pending | confirm_check | paid | paid_over | wrong_amount | completed | expired | cancelled | failed | refund_process | refund_failed | refund_paid`

## جدول Mapping وضعیت‌ها
### Cryptomus
- confirm_check → confirm_check
- paid → paid
- paid_over → paid_over
- wrong_amount → wrong_amount
- cancel → cancelled
- system_fail → failed
- refund_process → refund_process
- refund_fail → refund_failed
- refund_paid → refund_paid

### Coinbase Commerce
- charge:created → unpaid
- charge:pending → pending
- charge:confirmed → paid
- charge:failed → failed

### CoinPayments
- status:0/1 → pending
- status:2/100 → paid
- status:-1 → cancelled
- status:-2 → failed
- status:3 → paid_over

### BTCPay
- status=New → unpaid
- status=Processing → pending
- status=Expired → expired
- status=Settled/Complete → completed
- additionalStatus=PaidOver → paid_over
- additionalStatus=PaidPartial → wrong_amount
- additionalStatus=PaidLate → paid (با فلگ paidLate)

## قوانین Idempotency
- `crypto_invoices`: unique (tenant_id, provider, order_id)
- `crypto_webhook_calls`: provider + event_id یا provider + external_id + status + txid

## فریم‌ورک وبهوک
- دریافت raw payload + headers + IP
- verify signature + IP allowlist
- ذخیره در crypto_webhook_calls
- ACK سریع (HTTP 200)
- پردازش async با Job و DLQ
- امکان replay از UI

## آشتی‌سازی (Reconcile)
- هر ۵ دقیقه: query وضعیت invoice های unpaid/pending/confirm_check
- ایجاد SyntheticEvent در نبود وبهوک
- هر شب: مقایسه مجموع Ledger با provider balances

## Ledger دوطرفه (نمونه)
- Invoice paid:
  - Dr: Crypto Clearing
  - Cr: Merchant Payable یا Sales Revenue
- Platform fee:
  - Dr: Fee Expense
  - Cr: Platform Revenue
- Payout:
  - Dr: Merchant Payable
  - Cr: Crypto Wallet

## Workflow برداشت (Approval + Whitelist)
- ایجاد برداشت در حالت `pending_approval` ثبت می‌شود (قابل پیکربندی).
- فقط آدرس‌های موجود در `crypto_payout_destinations` مجاز هستند.
- تایید/رد از UI انجام و سپس برداشت به Provider ارسال می‌شود.

## Provider Patterns (الزامی)
### Cryptomus
- Auth header: merchant + sign = md5(base64(body)+API_KEY)
- Create invoice params: amount, currency, order_id, network, url_callback, is_payment_multiple, lifetime, to_currency, subtract, accuracy_payment_percent
- Webhook verify: IP allowlist 91.227.144.54 + امضا در بدنه (sign = md5(base64(payload بدون sign)+API_KEY))
- Statuses: confirm_check, paid, paid_over, wrong_amount, cancel, system_fail, refund_process, refund_fail, refund_paid

### Coinbase Commerce
- Webhook signature: X-CC-Webhook-Signature = HMAC-SHA256(raw payload, shared secret)
- Retry backoff تا ۳ روز (endpoint باید سریع ACK دهد)
- Events: charge:created, charge:pending, charge:confirmed, charge:failed

### CoinPayments
- Webhook signature + IP allowlist (hook1/hook2)
- Webhook ممکن است دیر برسد ⇒ reconcile/polling الزامی

### BTCPay
- Webhook signature: HMAC-SHA256 با secret
- status + additionalStatus هر دو بررسی شوند

## API (Public)
Base: `/api/v1/crypto`
- POST   /invoices
- GET    /invoices/{id}
- GET    /invoices/{id}/status
- POST   /invoices/{id}/refresh
- POST   /payouts
- GET    /payouts/{id}
- POST   /payouts/{id}/approve
- POST   /payouts/{id}/reject
- GET    /payout-destinations
- POST   /payout-destinations
- GET    /payout-destinations/{id}
- PUT    /payout-destinations/{id}
- DELETE /payout-destinations/{id}
- POST   /webhooks/{provider}
- GET    /rates?from=&to=
- GET    /health/providers
- GET    /health/nodes
- POST   /reconcile/run
- GET    /policy

## امنیت و عملیات (Hardening)
- Secrets encryption + redact logs
- Webhook: signature + IP allowlist
- Rate limit + WAF friendly responses
- Idempotency everywhere
- Queues: webhook processing async
- Timeouts/retries/backoff برای HTTP clients

## AI Auditor (فقط مالی)
- گزارش دوره‌ای: حجم، موفقیت، کارمزد، تاخیر confirmations
- anomaly: اختلاف webhook/polling، تکرار txid، paidLate، wrong_amount spikes
- خروجی: گزارش فارسی + JSON + ارسال با notify-core

## تصمیم‌ها
- Adapterها و DTOها در crypto-gateway پیاده می‌شوند.
- BTCPay fallback در crypto-nodes نگهداری می‌شود تا تفکیک self-hosted حفظ شود.
- Fee/Plan از SubscriptionPlan.features خوانده می‌شود و در صورت نبود، از crypto_fee_policies استفاده می‌شود.

## Backlog (اولویت بعدی)
- کیف‌پول چندامضایی و کنترل‌های AML داخلی (بدون نظارت فردی)
- کانکتورهای نود BTC/EVM کامل
- UI پیشرفته برای SLA و نرخ‌ها
