# SPEC — filament-payments

## معرفی
- پکیج: haida/filament-payments
- توضیح: Payments framework for Hub.
- Service Provider: Haida\FilamentPayments\FilamentPaymentsServiceProvider
- Filament Plugin: Haida\FilamentPayments\FilamentPaymentsPlugin (id: filament-payments)

## دامنه و قابلیت‌ها
- مدل‌ها:
- PaymentAttempt.php
- PaymentIntent.php
- PaymentProviderConnection.php
- PaymentReconciliation.php
- PaymentRefund.php
- PaymentWebhookEvent.php
- منابع Filament:
- src/Filament/Resources/PaymentIntentResource.php
- src/Filament/Resources/PaymentProviderConnectionResource.php
- src/Filament/Resources/PaymentReconciliationResource.php
- src/Filament/Resources/PaymentRefundResource.php
- src/Filament/Resources/PaymentWebhookEventResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/OpenApiController.php
- Api/V1/PaymentIntentController.php
- Api/V1/WebhookController.php
- Jobs/Queue:
- ندارد
- Policyها:
- PaymentIntentPolicy.php
- PaymentProviderConnectionPolicy.php
- PaymentReconciliationPolicy.php
- PaymentRefundPolicy.php
- PaymentWebhookEventPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): payments.manage, payments.view, payments.webhooks.manage

## مدل داده
- Migrations:
- 2026_01_02_000002_create_payments_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-payments/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-payments/config/filament-payments.php
- کلیدهای env مرتبط:
- INTL_REDIRECT_GATEWAY_REDIRECT_URL
- IRAN_REST_GATEWAY_CALLBACK_URL
- IRAN_REST_GATEWAY_ENDPOINT
- IRAN_REST_GATEWAY_FAKE
- IRAN_REST_GATEWAY_MERCHANT_ID
- IRAN_SOAP_GATEWAY_REDIRECT_URL
- IRAN_SOAP_GATEWAY_WSDL

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
