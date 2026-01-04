# Upgrade Gap Matrix

| Legacy / Desired Feature | Current state in module | Proposed implementation | Complexity | Risk | Tests | File areas |
| --- | --- | --- | --- | --- | --- | --- |
| Ledger limits (max charge/transaction) | Not in `PettyCashFund` | Add fields + validation + workflow thresholds | M | Med | fund validation, workflow guards | `packages/filament-petty-cash-ir/src/Models`, migrations, config |
| Transaction types (charge/expense/adjustment) | Expense + replenishment only | Add Adjustment model + API + UI | M | Med | CRUD + posting rules | models, migrations, Filament resource, API |
| Multi‑step approvals | Single step | Workflow engine with steps | H | Med | approval step tests | new Application/Domain + migrations |
| Separation of duties | Not enforced | Add actor guards + override | M | Med | approval actor tests | use-cases + policies |
| Revision / under review statuses | Not present | Add workflow states + exception tagging | M | Med | state machine tests | domain enums + services |
| Settlement cycles with archive reports | Basic settlement only | Add cycle summary + report export | M | Med | archive report export | services, report export |
| Shift handover cash count | Not present | Add CashCount / Handover model + UI | M | Med | discrepancy & approval | migrations + Filament resource |
| Alerts & exception inbox | Not present | Exception model + rules | M | Med | exception creation tests | new models + services |
| Smart invoice extraction | Not present | AI receipt extraction (opt‑in) | H | Med | fake provider tests | AI interface + UI |
| Duplicate receipt detection | Not present | Hash + metadata heuristic | M | Low | exception creation | services + controls |
| KPI dashboard (non‑surveillance) | Not present | Controls dashboard, trends | M | Med | report queries | Filament pages/widgets |
| Notifications (pending approvals) | Not present | notify‑core triggers | M | Low | notification dispatch | notify integration |
| Attachment metadata normalization | Minimal | Metadata schema + validation hooks | M | Low | metadata validation tests | attachments service |
| Physical document received | Not present | Evidence receipt flag + audit | S | Low | fields updated | migrations + UI |
| API OpenAPI completeness | Minimal summaries | Full schema + auth info + new endpoints | M | Low | OpenAPI snapshot | `Support/PettyCashOpenApi.php` |
| Idempotent posting + reversal | Not present | Idempotency keys + reverse endpoints | H | Med | idempotency tests | use‑cases + migrations |
| Tenant isolation | Implicit | Explicit query scopes + tests | M | High | tenant isolation tests | controllers + tests |
| Reporting exports | Not present | Streamed CSV/Excel export | M | Med | export permission tests | reports + API |
| Audit logging | Basic events | Extend for new actions + AI | M | Low | audit log tests | audit logger service |

Legend: S=small, M=medium, H=high.
