# API — mailtrap-core

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/mailtrap/inboxes | mailtrap.inbox.view | mailtrap.inbox.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/mailtrap/inboxes/sync | mailtrap.inbox.sync | mailtrap.inbox.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/mailtrap/messages | mailtrap.message.view | mailtrap.message.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/mailtrap/messages/{message} | mailtrap.message.view | mailtrap.message.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/mailtrap/messages/{message}/body | mailtrap.message.view | mailtrap.message.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/mailtrap/messages/{message}/attachments | mailtrap.message.view | mailtrap.message.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/mailtrap/messages/{message}/attachments/{attachment} | mailtrap.message.view | mailtrap.message.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/mailtrap/domains | mailtrap.domain.view | mailtrap.domain.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/mailtrap/domains/sync | mailtrap.domain.sync | mailtrap.domain.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/mailtrap/audiences/{audience}/contacts | mailtrap.audience.view | mailtrap.audience.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/mailtrap/audiences/{audience}/contacts | mailtrap.audience.manage | mailtrap.audience.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PUT | /api/v1/mailtrap/audiences/{audience}/contacts/{contact} | mailtrap.audience.manage | mailtrap.audience.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/mailtrap/audiences/{audience}/contacts/{contact} | mailtrap.audience.manage | mailtrap.audience.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/mailtrap/campaigns/{campaign}/send | mailtrap.campaign.send | mailtrap.campaign.send | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/mailtrap/openapi | mailtrap.connection.view | mailtrap.connection.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
