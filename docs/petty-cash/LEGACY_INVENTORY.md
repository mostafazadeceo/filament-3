# Legacy Petty Cash Inventory (tankha)

Source: `/tmp/tankha` (cloned) and `/tmp/tankha-petty` (module zip extracted).

## Core domain entities
- PettyCashLedger (ledger per branch with limits, balances, custodian, bank info) — `app/Models/PettyCashLedger.php`, migrations in `database/migrations/2025_10_18_182158_create_petty_cash_ledgers_table.php` + follow-ups.
- PettyCashTransaction (charge/expense/adjustment; statuses incl. submitted/approved/rejected/needs_changes/under_review; metadata; attachments) — `app/Models/PettyCashTransaction.php`, migrations in `database/migrations/2025_10_18_182204_create_petty_cash_transactions_table.php` + follow-ups.
- PettyCashCycle (settlement cycle with open/pending_close/closed; totals; report_path) — `app/Models/PettyCashCycle.php`, `database/migrations/2025_10_21_010000_create_petty_cash_cycles_table.php` + archive fields.
- PettyCashShiftHandoff (shift open/close cash count, discrepancies, approvals) — `database/migrations/2025_12_17_140220_create_petty_cash_shift_handoffs_table.php` + approval fields.
- AlertSetting (thresholds for low balance, pending transactions, high expense rate, duplicate detection) — `database/seeders/AlertSettingsSeeder.php`.

## Transaction lifecycle + approvals
- Types: charge, expense, adjustment — `app/Models/PettyCashTransaction.php`.
- Statuses: draft, submitted, approved, rejected, needs_changes, under_review — `app/Models/PettyCashTransaction.php`.
- Actions:
  - submit/approve/reject
  - send back for revision (needs_changes)
  - mark suspicious (under_review + metadata)
  - delete with soft-delete and deleted_by (`database/migrations/2025_12_10_165420_add_soft_deletes_to_petty_cash_transactions_table.php`).

## Workflow features
- Charge request flow (self-service + attachment) — `app/Livewire/PettyCash/ChargeRequestForm.php`.
- Settlement / archive cycle flow (request by branch, approve by admin, generate archive report, reset ledger opening balance) — `app/Livewire/PettyCash/SettlementPanel.php`, `app/Services/PettyCash/PettyCashArchiveService.php`.
- Shift handover (cash count + discrepancy + approvals) — migrations above + routes in `routes/web.php`.

## Reporting & analytics
- Ledger analytics (period filters, category/vendor breakdown, daily trend, recent transactions) — `app/Services/PettyCash/PettyCashService.php`.
- Charge usage timeline — `app/Services/PettyCash/PettyCashService.php`.
- KPI dashboard + scoring + overrides — `app/Services/PettyCash/PettyCashKpiService.php`, `app/Http/Controllers/Backend/PettyCashController.php` (kpi routes).
- Archive reports (Excel/HTML export) — `app/Services/PettyCash/PettyCashArchiveService.php`.
- Print/export routes — `routes/web.php`.

## AI / smart invoice (legacy)
- Smart invoice extraction using Gemini/OpenAI; confidence + line items + vendor metadata — `app/Services/PettyCash/SmartInvoiceService.php`, `app/Services/PettyCash/Data/SmartInvoiceExtraction.php`, config `config/smart-invoice.php`.
- UI integration for AI extraction in transaction form — `app/Livewire/PettyCash/TransactionForm.php`.
- Debug invoice upload page — `routes/web.php` + `app/Http/Controllers/Backend/PettyCash/DebugInvoiceUploadController.php`.

## Attachments & evidence
- Media library collections: invoice, bank_receipt, charge_request — `app/Models/PettyCashTransaction.php`.
- Physical documents received flags — `database/migrations/2025_12_17_125048_add_physical_documents_fields_to_petty_cash_transactions_table.php`.
- Invoice number/date fields — `database/migrations/2025_12_03_000001_add_invoice_number_to_petty_cash_transactions.php`, `2025_12_18_000001_add_invoice_date_to_petty_cash_transactions.php`.

## UI pages/screens (admin)
- Ledger index & analytics dashboard — `resources/views/backend/pages/petty-cash/index.blade.php`.
- Transactions page + livewire components — `resources/views/backend/pages/petty-cash/transactions.blade.php`, `app/Livewire/PettyCash/*`.
- Charge request page — `resources/views/backend/pages/petty-cash/charge-request.blade.php`.
- Settlement page — `resources/views/backend/pages/petty-cash/settlement.blade.php`.
- Archives list/edit/show — `resources/views/backend/pages/petty-cash/archives*.blade.php`.
- KPI dashboard — `resources/views/backend/pages/petty-cash/kpi-dashboard.blade.php` (in main repo).
- Backups + module export — `resources/views/backend/pages/petty-cash/backups.blade.php`.
- Debug invoice upload — `resources/views/backend/pages/petty-cash/debug-upload.blade.php`.

## Permissions (legacy naming)
- Ledger: `petty_cash.ledger.view/create/edit/delete`.
- Transaction: `petty_cash.transaction.view/create/edit/delete/approve/reject`.
- Archive: `petty_cash.archive.view/manage`.
Source: `app/Console/Commands/TranslatePermissions.php`.

## Data schema summary (legacy tables)
- `petty_cash_ledgers`: branch_name, limits, balances, assigned_user_id, bank info fields, settings.
- `petty_cash_transactions`: type, status, amount, currency, transaction_date, category, invoice_number/date, physical_documents_received*, archive_cycle_id, charge_origin, meta.
- `petty_cash_cycles`: status, opening/closing balances, requested_close/closed, totals, summary, report_path.
- `petty_cash_shift_handoffs`: cash count, discrepancy, approvals.

## Edge cases / operational concerns
- Duplicate transaction detection (alert setting), high expense rate alert, overdue settlement alert — `database/seeders/AlertSettingsSeeder.php`.
- Cycle finalization blocks when pending transactions exist — `app/Services/PettyCash/PettyCashArchiveService.php`.
- KPI override per transaction (manual) — `app/Http/Controllers/Backend/PettyCashController.php`.
- Smart invoice validation (tolerance + currency normalization) — `config/smart-invoice.php` + `TransactionForm` validations.
- Scheduled archive commands — `app/Console/Kernel.php` + `app/Console/Commands/PettyCashArchive.php`.
