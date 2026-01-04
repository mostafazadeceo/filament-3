# API — Meetings AI

## Base Path
- `/api/v1/meetings`

## Middleware
- `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:<scope>`

## Endpoints
- GET `/` (list meetings)
- POST `/` (create meeting)
- GET `/{id}` (show meeting)
- PUT `/{id}` (update meeting)
- DELETE `/{id}` (delete meeting)
- GET `/{id}/attendees`
- POST `/{id}/attendees`
- PUT `/attendees/{id}`
- DELETE `/attendees/{id}`
- GET `/{id}/agenda-items`
- POST `/{id}/agenda-items`
- PUT `/agenda-items/{id}`
- DELETE `/agenda-items/{id}`
- CRUD `/templates`
- POST `/{id}/consent/confirm`
- POST `/{id}/transcript/upload`
- POST `/{id}/transcript/manual`
- POST `/{id}/ai/generate-agenda`
- POST `/{id}/ai/generate-minutes`
- POST `/{id}/ai/recap`
- GET  `/{id}/minutes/export` (خروجی Markdown)
- POST `/{id}/action-items/link-to-workhub`
- GET `/openapi`

## Scopes (مثال)
- `meetings.view`
- `meetings.manage`
- `meetings.transcript.manage`
- `meetings.minutes.manage`
- `meetings.ai.use`
- `workhub.work_item.manage` (برای لینک اکشن‌آیتم به ورک‌هاب)

## OpenAPI
- اسناد OpenAPI از طریق filament-api-docs-builder منتشر می‌شود.

## نکته
- اگر صف‌گذاری AI فعال باشد، خروجی برخی عملیات با وضعیت `202` و `queued=true` بازمی‌گردد.
