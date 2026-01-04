# API — filament-workhub

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/workhub/work-items/{workItem}/transition | workhub.transition.manage | workhub.transition.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/workhub/work-items/{workItem}/comments | workhub.comment.view | workhub.comment.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/comments | workhub.comment.manage | workhub.comment.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/comments/{comment} | workhub.comment.manage | workhub.comment.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/ai/personal-summary | workhub.ai.use | workhub.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/work-items/{workItem}/ai/shared-summary | workhub.ai.share | workhub.ai.share | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/work-items/{workItem}/ai/thread-summary | workhub.ai.use | workhub.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/work-items/{workItem}/ai/generate-subtasks | workhub.ai.use | workhub.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/work-items/{workItem}/ai/progress-update | workhub.ai.use | workhub.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/work-items/{workItem}/ai/find-similar | workhub.ai.use | workhub.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/workhub/projects/{project}/ai/executive-summary | workhub.ai.project_reports.manage | workhub.ai.project_reports.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/workhub/projects/{project}/ai/stuck-tasks | workhub.ai.project_reports.manage | workhub.ai.project_reports.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/work-items/{workItem}/attachments | workhub.attachment.view | workhub.attachment.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/attachments | workhub.attachment.manage | workhub.attachment.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/attachments/{attachment} | workhub.attachment.manage | workhub.attachment.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/work-items/{workItem}/watchers | workhub.watcher.view | workhub.watcher.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/watchers | workhub.watcher.manage | workhub.watcher.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/watchers/{watcher} | workhub.watcher.manage | workhub.watcher.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/work-items/{workItem}/time-entries | workhub.time_entry.view | workhub.time_entry.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/time-entries | workhub.time_entry.manage | workhub.time_entry.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/time-entries/{timeEntry} | workhub.time_entry.manage | workhub.time_entry.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/work-items/{workItem}/decisions | workhub.decision.view | workhub.decision.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/decisions | workhub.decision.manage | workhub.decision.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/decisions/{decision} | workhub.decision.manage | workhub.decision.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/work-items/{workItem}/links | workhub.link.view | workhub.link.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/workhub/work-items/{workItem}/links | workhub.link.manage | workhub.link.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/workhub/links/{link} | workhub.link.manage | workhub.link.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/workhub/openapi | workhub.project.view | workhub.project.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
