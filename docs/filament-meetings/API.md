# API — filament-meetings

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | / | meetings.view | meetings.view | [ASSUMPTION] 60,1 | - |
| POST | / | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/meetings/{meeting} | meetings.view | meetings.view | [ASSUMPTION] 60,1 | - |
| PUT | /api/v1/meetings/{meeting} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/meetings/{meeting} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/meetings/{meeting}/attendees | meetings.view | meetings.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/meetings/{meeting}/attendees | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PUT | /api/v1/meetings/attendees/{attendee} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/meetings/attendees/{attendee} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/meetings/{meeting}/agenda-items | meetings.view | meetings.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/meetings/{meeting}/agenda-items | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PUT | /api/v1/meetings/agenda-items/{agendaItem} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/meetings/agenda-items/{agendaItem} | meetings.manage | meetings.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/meetings/{meeting}/consent/confirm | meetings.ai.use | meetings.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/meetings/{meeting}/transcript/upload | meetings.transcript.manage | meetings.transcript.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/meetings/{meeting}/transcript/manual | meetings.transcript.manage | meetings.transcript.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/meetings/{meeting}/ai/generate-agenda | meetings.ai.use | meetings.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/meetings/{meeting}/ai/generate-minutes | meetings.ai.use | meetings.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/meetings/{meeting}/ai/recap | meetings.ai.use | meetings.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/meetings/{meeting}/minutes/export | meetings.minutes.manage | meetings.minutes.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/meetings/{meeting}/action-items/link-to-workhub | meetings.action_items.manage | meetings.action_items.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/meetings/openapi | meetings.view | meetings.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
