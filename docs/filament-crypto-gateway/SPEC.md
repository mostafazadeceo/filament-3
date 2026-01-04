# SPEC — filament-crypto-gateway

## معرفی
- پکیج: haida/filament-crypto-gateway
- توضیح: Crypto gateway providers, API, webhooks, and orchestration.
- Service Provider: Haida\FilamentCryptoGateway\FilamentCryptoGatewayServiceProvider
- Filament Plugin: Haida\FilamentCryptoGateway\FilamentCryptoGatewayPlugin (id: filament-crypto-gateway)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CryptoAiReport.php
- CryptoInvoice.php
- CryptoInvoicePayment.php
- CryptoPayout.php
- CryptoPayoutDestination.php
- CryptoProviderAccount.php
- CryptoReconciliation.php
- CryptoWebhookCall.php
- منابع Filament:
- src/Filament/Resources/CryptoAiReportResource.php
- src/Filament/Resources/CryptoInvoicePaymentResource.php
- src/Filament/Resources/CryptoInvoiceResource.php
- src/Filament/Resources/CryptoPayoutDestinationResource.php
- src/Filament/Resources/CryptoPayoutResource.php
- src/Filament/Resources/CryptoProviderAccountResource.php
- src/Filament/Resources/CryptoReconciliationResource.php
- src/Filament/Resources/CryptoWebhookCallResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/HealthController.php
- Api/V1/InvoiceController.php
- Api/V1/OpenApiController.php
- Api/V1/PayoutController.php
- Api/V1/PayoutDestinationController.php
- Api/V1/PolicyController.php
- Api/V1/RateController.php
- Api/V1/ReconcileController.php
- Api/V1/WebhookController.php
- Jobs/Queue:
- ProcessWebhookCall.php
- Policyها:
- CryptoAiReportPolicy.php
- CryptoInvoicePaymentPolicy.php
- CryptoInvoicePolicy.php
- CryptoPayoutDestinationPolicy.php
- CryptoPayoutPolicy.php
- CryptoProviderAccountPolicy.php
- CryptoReconciliationPolicy.php
- CryptoWebhookCallPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): crypto.fee_policies.view, crypto.invoices.manage, crypto.invoices.view, crypto.nodes.view, crypto.payout_destinations.manage, crypto.payout_destinations.view, crypto.payouts.approve, crypto.payouts.manage, crypto.payouts.view, crypto.providers.manage, crypto.rates.view, crypto.reconcile.run, crypto.webhooks.manage

## مدل داده
- Migrations:
- 2026_01_02_000011_create_crypto_gateway_tables.php
- 2026_01_10_000002_create_crypto_gateway_tables.php
- 2026_01_10_000004_add_crypto_payout_whitelist_and_approvals.php
- جدول‌ها:
- crypto_ai_reports
- crypto_invoice_payments
- crypto_invoices
- crypto_payout_destinations
- crypto_payouts
- crypto_provider_accounts
- crypto_reconciliations
- crypto_webhook_calls
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-crypto-gateway/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-crypto-gateway/config/filament-crypto-gateway.php
- کلیدهای env مرتبط:
- COINBASE_COMMERCE_BASE_URL
- COINPAYMENTS_BASE_URL
- CRYPTOMUS_BASE_URL
- CRYPTO_AI_N8N_SECRET
- CRYPTO_AI_N8N_URL
- CRYPTO_AI_SECRET
- CRYPTO_AI_WEBHOOK_URL
- CRYPTO_API_RATE_LIMIT
- CRYPTO_GATEWAY_FAKE
- CRYPTO_NOTIFY_AUDIT_EVENT
- CRYPTO_NOTIFY_INVOICE_PAID_EVENT
- CRYPTO_NOTIFY_PANEL

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
