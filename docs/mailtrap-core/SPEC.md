# SPEC — mailtrap-core

## معرفی
- پکیج: haida/mailtrap-core
- توضیح: Mailtrap core integration (API client, models, services, sync).
- Service Provider: Haida\MailtrapCore\MailtrapCoreServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- MailtrapAudience.php
- MailtrapAudienceContact.php
- MailtrapCampaign.php
- MailtrapCampaignSend.php
- MailtrapConnection.php
- MailtrapInbox.php
- MailtrapMessage.php
- MailtrapOffer.php
- MailtrapSendingDomain.php
- MailtrapSingleSend.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/AudienceContactController.php
- Api/V1/AudienceController.php
- Api/V1/CampaignController.php
- Api/V1/ConnectionController.php
- Api/V1/DomainController.php
- Api/V1/InboxController.php
- Api/V1/MessageController.php
- Api/V1/OfferController.php
- Api/V1/OpenApiController.php
- Api/V1/SingleSendController.php
- Jobs/Queue:
- MailtrapCampaignSendJob.php
- Policyها:
- MailtrapAudiencePolicy.php
- MailtrapCampaignPolicy.php
- MailtrapConnectionPolicy.php
- MailtrapInboxPolicy.php
- MailtrapMessagePolicy.php
- MailtrapOfferPolicy.php
- MailtrapSendingDomainPolicy.php
- MailtrapSingleSendPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): mailtrap.audience.manage, mailtrap.audience.view, mailtrap.campaign.send, mailtrap.connection.view, mailtrap.domain.sync, mailtrap.domain.view, mailtrap.inbox.sync, mailtrap.inbox.view, mailtrap.message.view

## مدل داده
- Migrations:
- 2026_01_02_000001_create_mailtrap_connections_table.php
- 2026_01_02_000002_create_mailtrap_inboxes_table.php
- 2026_01_02_000003_create_mailtrap_messages_table.php
- 2026_01_02_000004_create_mailtrap_sending_domains_table.php
- 2026_01_02_000005_create_mailtrap_offers_table.php
- 2026_01_02_000006_add_send_api_token_to_mailtrap_connections_table.php
- 2026_01_02_000007_create_mailtrap_audiences_table.php
- 2026_01_02_000008_create_mailtrap_audience_contacts_table.php
- 2026_01_02_000009_create_mailtrap_campaigns_table.php
- 2026_01_02_000010_create_mailtrap_campaign_sends_table.php
- 2026_01_02_000011_create_mailtrap_single_sends_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/mailtrap-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/mailtrap-core/config/mailtrap-core.php
- کلیدهای env مرتبط:
- MAILTRAP_API_RATE_LIMIT
- MAILTRAP_BASE_URL
- MAILTRAP_FAKE
- MAILTRAP_FAKE_RUN_ID
- MAILTRAP_HTTP_RETRY_SLEEP
- MAILTRAP_HTTP_RETRY_TIMES
- MAILTRAP_HTTP_TIMEOUT
- MAILTRAP_LOGGING_ENABLED
- MAILTRAP_RATE_MAX
- MAILTRAP_RATE_SECONDS
- MAILTRAP_SEND_BASE_URL
- MAILTRAP_SYNC_MIN_SECONDS

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
