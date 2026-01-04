# Innovation Backlog (World‑Class Petty Cash)

Each item includes value, risks, permissions, implementation approach, and test approach.

## Workflow & approvals
1) Multi‑step approval chains by amount/category/fund
- Value: Enforces separation of duties and reduces fraud exposure.
- Risks: Complex configs could block operations.
- Permissions: `petty_cash.workflow.manage`, `petty_cash.expense.approve`.
- Approach: JSON workflow rules + approval step tracking on records.
- Tests: approval step ordering, rejection at step, tenant isolation.

2) Dynamic thresholds (max per transaction, max per day)
- Value: Prevents oversized or burst spending.
- Risks: False positives without configurable thresholds.
- Permissions: `petty_cash.controls.manage`.
- Approach: Control rules table + evaluator; create exceptions when breached.
- Tests: rule evaluation across thresholds, exception creation.

3) Separation‑of‑duties enforcement
- Value: Prevents same user from submit+approve+pay.
- Risks: Small teams may need override.
- Permissions: `petty_cash.workflow.override`.
- Approach: Workflow guard checks on actor roles; allow override with audit.
- Tests: prevent self‑approve; override audit log.

4) Action‑level idempotency
- Value: Eliminates double‑posting and duplicate ledgers.
- Risks: Incorrect keys could block legitimate retries.
- Permissions: `petty_cash.expense.post`, `petty_cash.replenishment.post`.
- Approach: Idempotency table + key constraints for post/reverse.
- Tests: repeat post with same key is no‑op; different key rejects if already posted.

## Controls, reconciliation, cash count
5) Cash count workflow (per shift or date)
- Value: Detects variances early.
- Risks: Extra operational workload.
- Permissions: `petty_cash.controls.cash_count`.
- Approach: CashCount model with evidence attachments + approval.
- Tests: variance detection, approval gating, tenant scoping.

6) Reconciliation workflow (fund vs ledger)
- Value: Ensures accounting alignment.
- Risks: Requires integration accuracy.
- Permissions: `petty_cash.controls.reconcile`.
- Approach: Reconcile model with expected vs actual balances; exception creation.
- Tests: reconciliation run idempotent; exceptions for variance.

7) Exception inbox + lifecycle
- Value: Centralized control failures resolution.
- Risks: Noise if rules too sensitive.
- Permissions: `petty_cash.exceptions.view/manage`.
- Approach: Exception model + status transitions (open/triaged/resolved).
- Tests: create/resolve exceptions; permissions enforced.

8) Duplicate receipt detection hooks
- Value: Reduce duplicate reimbursements.
- Risks: False matches if fuzzy logic too strict.
- Permissions: `petty_cash.controls.manage`.
- Approach: hash + metadata similarity; create exception.
- Tests: same hash triggers exception; unique receipts pass.

## Reporting & analytics
9) Fund health dashboard
- Value: Quick view of balances, burn rate, thresholds.
- Risks: Data accuracy; N+1 queries.
- Permissions: `petty_cash.report.view`.
- Approach: aggregated queries + caching.
- Tests: aggregates match fixture data; eager loads.

10) Controls dashboard (exceptions, SLA)
- Value: Shows control failures, aging, resolution time.
- Risks: Requires new models.
- Permissions: `petty_cash.exceptions.view`.
- Approach: exceptions table + reporting widgets.
- Tests: SLA calculation correctness.

11) Settlement & archive reporting (Excel/CSV)
- Value: Audit‑ready exports.
- Risks: Large datasets, performance.
- Permissions: `petty_cash.report.export`.
- Approach: streamed exports with filters.
- Tests: export format + access control.

12) Category/vendor spend analytics
- Value: Spend insights for cost control.
- Risks: Missing metadata; requires clean data.
- Permissions: `petty_cash.report.view`.
- Approach: aggregate by category/party + Jalali dates.
- Tests: grouping and totals.

## Attachments & evidence pipeline
13) Attachment metadata normalization
- Value: Standardized receipt metadata for AI/controls.
- Risks: Inconsistent uploader inputs.
- Permissions: `petty_cash.expense.manage`.
- Approach: metadata schema + validation hook.
- Tests: invalid metadata rejected; schema stored.

14) Multi‑evidence support (invoice/receipt/bank)
- Value: Complete audit trail per transaction.
- Risks: UI complexity.
- Permissions: `petty_cash.expense.manage`.
- Approach: evidence type enum + storage interface.
- Tests: attach multiple evidence types, fetch in API.

## Integrations
15) Accounting adapter with reversal support
- Value: Reliable postings and reversals.
- Risks: External service errors.
- Permissions: `petty_cash.expense.post`, `petty_cash.expense.reverse`.
- Approach: Adapter interface + default IR implementation.
- Tests: reversal idempotency + fund balance restored.

16) Notification dispatch via notify‑core
- Value: Multi‑channel alerts on approvals/exceptions.
- Risks: Spam without throttling.
- Permissions: `petty_cash.notifications.manage`.
- Approach: TriggerDispatcher rules + templated notifications.
- Tests: notification fired on exception.

17) Import pipeline (CSV)
- Value: Faster backfills and migrations.
- Risks: Data integrity.
- Permissions: `petty_cash.import.manage`.
- Approach: queued importer + validation report.
- Tests: invalid rows reported; tenant scoping.

## AI continuous auditor (safe, opt‑in)
18) Receipt field extraction (opt‑in)
- Value: Reduces manual data entry errors.
- Risks: Privacy; accuracy.
- Permissions: `petty_cash.ai.use`.
- Approach: Provider interface + fake provider; store only structured fields.
- Tests: AI disabled -> no calls; accepted suggestions logged.

19) Anomaly detection (controls only)
- Value: Early detection of risky spend patterns.
- Risks: False positives; must avoid employee scoring.
- Permissions: `petty_cash.ai.view_reports`.
- Approach: Rules + AI suggestions for exceptions; never score people.
- Tests: anomalies create exceptions; audit log present.

20) AI‑assisted category/account suggestion
- Value: Improves coding accuracy.
- Risks: Wrong classification.
- Permissions: `petty_cash.ai.use`.
- Approach: suggestions with confidence; user acceptance required.
- Tests: acceptance/rejection stored; no auto‑apply without consent.

21) AI governance & audit logs
- Value: Compliance and transparency.
- Risks: Sensitive data handling.
- Permissions: `petty_cash.ai.manage_settings`.
- Approach: audit table + prompt storage disabled by default.
- Tests: log event for each suggestion; retention policy enforced.

## Operational tooling
22) Scenario‑driven validation
- Value: Safe end‑to‑end checks across tenants.
- Risks: Side effects in production.
- Permissions: `petty_cash.scenario.run` (internal).
- Approach: idempotent scenario runner updates.
- Tests: runner re‑runs without duplication.

23) Data quality monitor
- Value: Detect missing receipts, orphaned records.
- Risks: Overhead on large data.
- Permissions: `petty_cash.controls.manage`.
- Approach: scheduled job creates exceptions for missing data.
- Tests: missing receipt triggers exception.

24) API rate limit overrides per tenant
- Value: Tuned for high‑volume tenants.
- Risks: abuse if misconfigured.
- Permissions: `petty_cash.api.manage`.
- Approach: tenant config overrides + middleware.
- Tests: per‑tenant throttling applied.

