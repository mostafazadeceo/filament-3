# API — filament-accounting-ir

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/accounting-ir/vat-periods/{vat_period}/generate | accounting.vat_report.manage | accounting.vat_report.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/vat-reports/{vat_report}/submit | accounting.vat_report.manage | accounting.vat_report.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/sales-invoices/{sales_invoice}/issue | accounting.sales.manage | accounting.sales.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/purchase-invoices/{purchase_invoice}/receive | accounting.purchase.manage | accounting.purchase.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/inventory-docs/{inventory_doc}/post | accounting.inventory.post | accounting.inventory.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/e-invoices/{e_invoice}/send | accounting.einvoice.send | accounting.einvoice.send | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/integrations/{integration_connector}/run | accounting.integration.manage | accounting.integration.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/accounting-ir/reports/trial-balance | accounting.report.view | accounting.report.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/accounting-ir/reports/general-ledger | accounting.report.view | accounting.report.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/accounting-ir/journal-entries/{journalEntry}/submit | accounting.journal.submit | accounting.journal.submit | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/journal-entries/{journalEntry}/approve | accounting.journal.approve | accounting.journal.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/journal-entries/{journalEntry}/post | accounting.journal.post | accounting.journal.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/accounting-ir/journal-entries/{journalEntry}/reverse | accounting.journal.reverse | accounting.journal.reverse | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/accounting-ir/openapi | accounting.view | accounting.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
