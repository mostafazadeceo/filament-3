# نصب و راه‌اندازی — پلتفرم کیف‌پول و درگاه رمزارز

## پیش‌نیازها
- PHP 8.2+ ، Laravel 11.28+ ، Filament v4
- صف فعال (Queue Worker) و Scheduler
- دسترسی به دیتابیس و اجرای مهاجرت‌ها

## مهاجرت‌ها
```
php artisan migrate
```
در محیط تولید:
```
php artisan migrate --force
```

## پیکربندی
فایل‌های تنظیمات:
- `config/filament-crypto-core.php`
- `config/filament-crypto-gateway.php`
- `config/filament-crypto-nodes.php`

کلیدهای مهم محیطی:
- `CRYPTOMUS_BASE_URL`
- `COINBASE_COMMERCE_BASE_URL`
- `COINPAYMENTS_BASE_URL`
- `CRYPTO_API_RATE_LIMIT`
- `CRYPTO_AI_N8N_URL` و `CRYPTO_AI_N8N_SECRET`
- `BTCPAY_BASE_URL` / `BTCPAY_API_KEY` / `BTCPAY_STORE_ID` / `BTCPAY_WEBHOOK_SECRET`
- `BITCOIN_RPC_URL` / `BITCOIN_RPC_USER` / `BITCOIN_RPC_PASSWORD`
- `EVM_RPC_URL`

## اتصال درگاه‌ها (Filament)
از پنل تننت/ادمین مسیر «درگاه رمزارز → اتصالات درگاه»:
- provider: `cryptomus` / `coinbase` / `coinpayments` / `btcpay`
- env: `sandbox` یا `prod`
- merchant_id
- api_key
- secret
- config_json (اختیاری)

نمونه `config_json` برای BTCPay:
```json
{
  "base_url": "https://btcpay.example.com",
  "api_key": "<token>",
  "store_id": "<store>",
  "webhook_secret": "<secret>"
}
```

نمونه `config_json` برای Bitcoin Core:
```json
{
  "rpc_url": "http://127.0.0.1:8332",
  "rpc_user": "rpcuser",
  "rpc_password": "rpcpass"
}
```

نمونه `config_json` برای EVM:
```json
{
  "rpc_url": "https://rpc.example.com"
}
```

## وبهوک‌ها
- مسیر عمومی: `POST /api/v1/crypto/webhooks/{provider}`
- وبهوک باید 200 سریع بگیرد؛ پردازش به صف منتقل می‌شود.
- IP Allowlist برای Cryptomus و CoinPayments فعال است.

## پلن و کارمزد
- ویژگی‌ها از `SubscriptionPlan.features` یا `config/filament-crypto-core.php` خوانده می‌شود.
- کلیدها:
  - `crypto.providers`
  - `crypto.payouts`
  - `crypto.webhook_replay`
  - `crypto.ai_auditor`
  - `crypto.nodes`

## برداشت و لیست سفید
- مقصدهای برداشت را در «درگاه رمزارز → لیست سفید برداشت» ثبت کنید.
- برداشت‌ها در صورت فعال بودن `payouts.require_approval` با وضعیت `pending_approval` ثبت می‌شوند.
- پیکربندی:
  - `filament-crypto-gateway.payouts.require_approval`
  - `filament-crypto-gateway.payouts.whitelist.enabled`

## AI Auditor
- فعال‌سازی سراسری: `filament-crypto-gateway.ai.enabled=true`
- فعال‌سازی پلنی: `crypto.ai_auditor=true`

## Release Checklist
- اجرای مهاجرت‌ها (`--force` در تولید)
- بررسی فعال بودن صف و Scheduler
- ثبت وبهوک‌ها و IP Allowlist
- تنظیم Secrets در `CryptoProviderAccount`
- ثبت آدرس‌های برداشت در لیست سفید
- تنظیم فرآیند تایید برداشت (Approval)
- تست `POST /api/v1/crypto/health/providers`
- بررسی Reconcile Scheduler
- بررسی دسترسی‌ها و Permissionها
