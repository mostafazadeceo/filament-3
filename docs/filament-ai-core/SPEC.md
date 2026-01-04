# SPEC — filament-ai-core

## معرفی
- پکیج: haida/filament-ai-core
- توضیح: AI core provider + governance for Filament v4.
- Service Provider: Haida\FilamentAiCore\FilamentAiCoreServiceProvider
- Filament Plugin: Haida\FilamentAiCore\FilamentAiCorePlugin (id: filament-ai-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- AiFeedback.php
- AiPolicy.php
- AiRequest.php
- منابع Filament:
- src/Filament/Resources/AiPolicyResource.php
- src/Filament/Resources/AiRequestResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- AiPolicyPolicy.php
- AiRequestPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2026_02_01_000001_create_ai_policies_table.php
- 2026_02_01_000002_create_ai_requests_table.php
- 2026_02_01_000003_create_ai_feedback_table.php
- جدول‌ها:
- ai_feedback
- ai_policies
- ai_requests
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-ai-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-ai-core/config/filament-ai-core.php
- کلیدهای env مرتبط:
- AI_CORE_ENABLED
- AI_CORE_PROVIDER
- AI_N8N_BASE_URL
- AI_N8N_ENABLED
- AI_N8N_IDEMPOTENCY_HEADER
- AI_N8N_NONCE_HEADER
- AI_N8N_NONCE_TTL_SECONDS
- AI_N8N_SECRET
- AI_N8N_SIGNATURE_HEADER
- AI_N8N_TIMEOUT
- AI_N8N_TIMESTAMP_HEADER
- AI_N8N_TOLERANCE_SECONDS
- AI_OPENAI_ENABLED
- OPENAI_API_KEY
- OPENAI_MODEL
- OPENAI_TIMEOUT

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
