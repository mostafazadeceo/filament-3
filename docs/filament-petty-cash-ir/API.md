# API — filament-petty-cash-ir

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/petty-cash/expenses/{expense}/submit | petty_cash.expense.manage | petty_cash.expense.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/approve | petty_cash.expense.approve | petty_cash.expense.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/reject | petty_cash.expense.reject | petty_cash.expense.reject | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/post | petty_cash.expense.post | petty_cash.expense.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/ai-suggest | petty_cash.ai.use | petty_cash.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/ai-apply | petty_cash.ai.use | petty_cash.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/expenses/{expense}/ai-reject | petty_cash.ai.use | petty_cash.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/replenishments/{replenishment}/submit | petty_cash.replenishment.manage | petty_cash.replenishment.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/replenishments/{replenishment}/approve | petty_cash.replenishment.approve | petty_cash.replenishment.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/replenishments/{replenishment}/reject | petty_cash.replenishment.reject | petty_cash.replenishment.reject | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/replenishments/{replenishment}/post | petty_cash.replenishment.post | petty_cash.replenishment.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/settlements/{settlement}/submit | petty_cash.settlement.manage | petty_cash.settlement.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/settlements/{settlement}/approve | petty_cash.settlement.approve | petty_cash.settlement.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/settlements/{settlement}/post | petty_cash.settlement.post | petty_cash.settlement.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/petty-cash/ai/audit | petty_cash.ai.use | petty_cash.ai.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/petty-cash/ai/report | petty_cash.ai.view_reports | petty_cash.ai.view_reports | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/petty-cash/openapi | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
