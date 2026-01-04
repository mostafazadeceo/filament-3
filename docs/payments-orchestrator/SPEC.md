# SPEC — payments-orchestrator

## معرفی
- پکیج: haida/payments-orchestrator
- توضیح: Payment orchestration for tenant gateways and webhooks.
- Service Provider: Haida\PaymentsOrchestrator\PaymentsOrchestratorServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- PaymentGatewayConnection.php
- PaymentIntent.php
- PaymentWebhookEvent.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/OpenApiController.php
- Api/V1/PaymentIntentController.php
- Api/V1/WebhookController.php
- Jobs/Queue:
- ندارد
- Policyها:
- PaymentIntentPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): commerce.payment.manage, commerce.payment.view

## مدل داده
- Migrations:
- 2025_12_30_000013_create_payments_orchestrator_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/payments-orchestrator/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/payments-orchestrator/config/payments-orchestrator.php
- کلیدهای env مرتبط:
- PAYMENTS_API_RATE_LIMIT
- PAYMENTS_ORCHESTRATOR_FAKE
- PAYMENTS_ORCHESTRATOR_FAKE_RUN_ID

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
