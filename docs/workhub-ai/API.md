# API — Workhub AI

## Base Path
- `/api/v1/workhub`

## Middleware
- `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:<scope>`

## Endpoints
- POST `/work-items/{id}/ai/personal-summary`
- POST `/work-items/{id}/ai/shared-summary`
- POST `/work-items/{id}/ai/thread-summary`
- POST `/work-items/{id}/ai/generate-subtasks`
- POST `/work-items/{id}/ai/progress-update`
- POST `/work-items/{id}/ai/find-similar`
- POST `/projects/{id}/ai/executive-summary`
- GET  `/projects/{id}/ai/stuck-tasks`

## Scopes (مثال)
- `workhub.ai.use`
- `workhub.ai.share`
- `workhub.ai.fields.manage`
- `workhub.ai.project_reports.manage`

## OpenAPI
- اسناد OpenAPI از طریق filament-api-docs-builder منتشر می‌شود.
