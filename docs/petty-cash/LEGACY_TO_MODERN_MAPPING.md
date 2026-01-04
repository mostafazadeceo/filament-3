# Legacy to Modern Mapping (tankha -> filament-petty-cash-ir)

| Legacy concept | Legacy source | Modern target | Notes / migration intent |
| --- | --- | --- | --- |
| PettyCashLedger (branch ledger) | `app/Models/PettyCashLedger.php` | PettyCashFund | Extend fund with limits, custodian, bank details, settings. Preserve balances + thresholds. |
| Ledger limits (limit_amount, max_charge_request_amount, max_transaction_amount) | ledger migrations | Fund limits + workflow thresholds | Add fields to funds or new fund_settings JSON for threshold rules. |
| PettyCashTransaction (expense/charge/adjustment) | `app/Models/PettyCashTransaction.php` | Expense + Replenishment + Adjustment | Map `expense` -> `PettyCashExpense`, `charge` -> `PettyCashReplenishment`, add `PettyCashAdjustment` model for adjustments. |
| Transaction statuses (draft/submitted/approved/rejected/needs_changes/under_review) | `PettyCashTransaction` | Workflow status + approval steps | Add workflow states & exception flags for revision/review; keep compatible with current paid/settled flow. |
| Attachments (invoice/receipt/bank/charge_request) | Media Library collections | ExpenseAttachment + Evidence | Add attachment metadata + validation hooks; support receipt/invoice/bank receipt types. |
| Smart invoice extraction (Gemini/OpenAI) | `SmartInvoiceService`, `TransactionForm` | AI continuous auditor (receipt extraction) | Provide provider interface + fake provider; store structured fields in metadata; opt-in + audit logged. |
| Cycle/Archive settlement (open/pending_close/closed) | `PettyCashCycle`, `PettyCashArchiveService` | Settlement + Reconciliation | Add settlement cycles + archived reports; block close if pending items. |
| Archive report (Excel/HTML) | `PettyCashArchiveService` | Settlement report export | Implement export endpoint with permission gating. |
| Shift handover (cash count, discrepancy, approval) | `petty_cash_shift_handoffs` | Cash Count / Reconciliation control | Model control runs + discrepancy exceptions + approvals. |
| KPI dashboard + scoring | `PettyCashKpiService`, KPI routes | Controls dashboard + exception trends | Avoid employee scoring; score only transactions/controls; provide management dashboards with opt-in. |
| Alerts (low balance, high expense rate, duplicates) | `AlertSettingsSeeder.php` | Control rules + exception inbox | Implement configurable controls with thresholds and exception records. |
| Notification center (pending approvals/archives) | `NotificationCenter` | Exception inbox + notifications | Map to Filament page + notifications via notify-core. |
| Backups / module export | `PettyCashController` | Export reports + optional backups | Consider optional export endpoints only; avoid host app changes unless required. |
| KPI override | `PettyCashController` | Exception annotation | Provide admin-only manual override of exception severity with audit log. |
| Debug invoice upload | `DebugInvoiceUploadController` | AI debug tools (admin-only) | Optional admin page behind `petty_cash.ai.manage_settings`. |
