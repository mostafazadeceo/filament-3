# Accounting IR API

## Base
- `/api/v1/accounting-ir`
- Auth: `ApiKeyAuth` + `ApiAuth` + `ResolveTenant`
- Rate limit: `config('filament-accounting-ir.api.rate_limit')`

## Core Endpoints (MVP)
- `GET /companies` / `POST /companies`
- `GET /companies/{company}` / `PUT /companies/{company}` / `DELETE /companies/{company}`
- `GET /company-settings` / `POST /company-settings`
- `GET /company-settings/{company_setting}` / `PUT /company-settings/{company_setting}` / `DELETE /company-settings/{company_setting}`
- `GET /branches` / `POST /branches`
- `GET /fiscal-years` / `POST /fiscal-years`
- `GET /fiscal-periods` / `POST /fiscal-periods`
- `GET /account-plans` / `POST /account-plans`
- `GET /chart-accounts` / `POST /chart-accounts`
- `GET /dimensions` / `POST /dimensions`
- `GET /journal-entries` / `POST /journal-entries`
- `GET /parties` / `POST /parties`
- `GET /products` / `POST /products`
- `GET /tax-categories` / `POST /tax-categories`
- `GET /uoms` / `POST /uoms`
- `GET /tax-rates` / `POST /tax-rates`
- `GET /vat-periods` / `POST /vat-periods`
- `POST /vat-periods/{vat_period}/generate`
- `GET /vat-reports` / `POST /vat-reports`
- `POST /vat-reports/{vat_report}/submit`
- `GET /withholding-rates` / `POST /withholding-rates`
- `GET /seasonal-reports` / `POST /seasonal-reports`
- `GET /sales-invoices` / `POST /sales-invoices`
- `POST /sales-invoices/{sales_invoice}/issue`
- `GET /purchase-invoices` / `POST /purchase-invoices`
- `POST /purchase-invoices/{purchase_invoice}/receive`
- `GET /treasury-accounts` / `POST /treasury-accounts`
- `GET /treasury-transactions` / `POST /treasury-transactions`
- `GET /cheques` / `POST /cheques`
- `GET /warehouses` / `POST /warehouses`
- `GET /inventory-items` / `POST /inventory-items`
- `GET /inventory-docs` / `POST /inventory-docs`
- `POST /inventory-docs/{inventory_doc}/post`
- `GET /fixed-assets` / `POST /fixed-assets`
- `GET /employees` / `POST /employees`
- `GET /payroll-runs` / `POST /payroll-runs`
- `GET /payroll-tables` / `POST /payroll-tables`
- `GET /projects` / `POST /projects`
- `GET /contracts` / `POST /contracts`
- `GET /e-invoice-providers` / `POST /e-invoice-providers`
- `GET /e-invoices` / `POST /e-invoices`
- `POST /e-invoices/{e_invoice}/send`
- `GET /key-materials` / `POST /key-materials`
- `GET /integrations` / `POST /integrations`
- `POST /integrations/{integration_connector}/run`
- `POST /journal-entries/{journalEntry}/submit`
- `POST /journal-entries/{journalEntry}/approve`
- `POST /journal-entries/{journalEntry}/post`
- `POST /journal-entries/{journalEntry}/reverse`
- `GET /reports/trial-balance`
- `GET /reports/general-ledger`

## Reports
### Trial Balance
`GET /reports/trial-balance?company_id=1&fiscal_year_id=3&from=2025-01-01&to=2025-03-31`

### General Ledger
`GET /reports/general-ledger?company_id=1&account_id=25&from=2025-01-01&to=2025-03-31`

## OpenAPI
- `GET /openapi`

## Idempotency
- برای عملیات مالی حساس از هدر `Idempotency-Key` استفاده کنید (مرحله بعدی).

## Webhooks
- نوع وبهوک: `accounting`
- رخدادها:
  - `journal_entry.posted`
  - `sales_invoice.posted`
  - `purchase_invoice.posted`
  - `treasury_transaction.posted`
  - `inventory_doc.posted`
  - `vat_report.submitted`
  - `e_invoice.sent`
  - `e_invoice.failed`

### نمونه Payload
```json
{
  "event": "sales_invoice.posted",
  "id": 1201,
  "tenant_id": 2,
  "company_id": 5,
  "status": "posted",
  "created_at": "2025-03-21T10:30:00+03:30"
}
```

```json
{
  "event": "e_invoice.failed",
  "id": 7002,
  "tenant_id": 2,
  "company_id": 5,
  "status": "failed",
  "created_at": "2025-03-21T10:31:00+03:30"
}
```
