# Petty Cash Upgrade Baseline (M0)

## Current domain objects (package)
- PettyCashFund (`packages/filament-petty-cash-ir/src/Models/PettyCashFund.php`): fund with balances, thresholds, accounting accounts, custodian.
- PettyCashCategory (`packages/filament-petty-cash-ir/src/Models/PettyCashCategory.php`): expense category, optional GL account.
- PettyCashExpense (`packages/filament-petty-cash-ir/src/Models/PettyCashExpense.php`): expense with lifecycle status, attachments, payment fields.
- PettyCashExpenseAttachment (`packages/filament-petty-cash-ir/src/Models/PettyCashExpenseAttachment.php`): file metadata only.
- PettyCashReplenishment (`packages/filament-petty-cash-ir/src/Models/PettyCashReplenishment.php`): fund top-up request/payment.
- PettyCashSettlement (`packages/filament-petty-cash-ir/src/Models/PettyCashSettlement.php`): period settlement and posting.
- PettyCashSettlementItem (`packages/filament-petty-cash-ir/src/Models/PettyCashSettlementItem.php`): link expenses to settlement.
- PettyCashAuditEvent (`packages/filament-petty-cash-ir/src/Models/PettyCashAuditEvent.php`): audit log entries.

## Current status flows (service-based)
- Expense: draft -> submitted -> approved -> paid -> settled (service in `packages/filament-petty-cash-ir/src/Services/PettyCashPostingService.php`).
- Replenishment: draft -> submitted -> approved -> paid (same service).
- Settlement: draft -> submitted -> approved -> posted (same service; marks expenses as settled).

## Current UI map (Filament resources)
- Funds: `packages/filament-petty-cash-ir/src/Filament/Resources/PettyCashFundResource.php`
- Categories: `packages/filament-petty-cash-ir/src/Filament/Resources/PettyCashCategoryResource.php`
- Expenses: `packages/filament-petty-cash-ir/src/Filament/Resources/PettyCashExpenseResource.php` + attachments relation manager
- Replenishments: `packages/filament-petty-cash-ir/src/Filament/Resources/PettyCashReplenishmentResource.php`
- Settlements: `packages/filament-petty-cash-ir/src/Filament/Resources/PettyCashSettlementResource.php` + settlement items

## Current API map (v1)
- Routes: `packages/filament-petty-cash-ir/routes/api.php`
- Controllers: `packages/filament-petty-cash-ir/src/Http/Controllers/Api/V1/*Controller.php`
- OpenAPI generator: `packages/filament-petty-cash-ir/src/Support/PettyCashOpenApi.php`
- Endpoints include CRUD and action endpoints for submit/approve/reject/post on expenses/replenishments/settlements; openapi path `/api/v1/petty-cash/openapi`.

## Current permissions (IAM capability registry)
- Capability registration: `packages/filament-petty-cash-ir/src/Support/PettyCashCapabilities.php`
- Policies: `packages/filament-petty-cash-ir/src/Policies/*`
- Permission set includes basic CRUD + approve/post/reject + report view/export.

## Current data/DB schema (migrations)
- Core tables: funds, categories (`packages/filament-petty-cash-ir/database/migrations/2026_01_01_000001_create_petty_cash_core_tables.php`).
- Expense tables + attachments + replenishments (`packages/filament-petty-cash-ir/database/migrations/2026_01_01_000002_create_petty_cash_expense_tables.php`).
- Settlement tables (`packages/filament-petty-cash-ir/database/migrations/2026_01_01_000003_create_petty_cash_settlement_tables.php`).
- Audit events (`packages/filament-petty-cash-ir/database/migrations/2026_01_01_000004_create_petty_cash_audit_tables.php`).

## Known pain points / code smells
- Single service (`PettyCashPostingService`) mixes validation, accounting integration, and auditing; no clear separation of domain/application/infrastructure layers.
- No explicit state machine or invariant enforcement outside service methods; status transitions only validated in service functions.
- No idempotency handling for post/reverse actions; repeated calls can create duplicate journals or double-adjust balances.
- Limited use of workflows: no multi-step approvals, threshold-based approvals, or separation-of-duties enforcement.
- API and UI listing queries lack explicit tenant scoping in code (assumes global scope from IAM); needs explicit guard in new code paths.
- Minimal reporting: no dashboards, exception inbox, or reconciliation tools.
- OpenAPI spec is minimal (summary-only, no schemas or security), missing new endpoints.
- No tests in package; scenario runner has a minimal happy-path petty cash flow only.
- Attachment pipeline is storage-path-only; no validation hooks, duplicate detection, or metadata normalization.

## Current indexes and risks
- Core indexes exist on tenant/company/status for main tables (see migrations), but no dedicated indexes for frequent filters like requested_by/approved_by, or for audit event query by subject.
- Potential N+1 in UI tables if relations not eager loaded (partial use of `HasEagerLoads` for expenses only).

## Baseline assumptions
- Tenant scoping is enforced via `Filamat\IamSuite\Support\BelongsToTenant` trait; new queries must still be scoped explicitly in new services.
- Existing API endpoints and permission names must remain stable.
