# Petty Cash Architecture (Clean Layering)

## Overview
The module is refactored into explicit layers to separate domain rules, application use‑cases, and infrastructure adapters. Existing controllers/Filament resources call a single application service (`PettyCashPostingService`) that delegates to use‑cases.

## Layering
- Domain
  - Rules enforce invariants for state transitions.
  - Files: `packages/filament-petty-cash-ir/src/Domain/Rules/*`.
- Application
  - Use‑cases implement business flows (submit/approve/post/reverse) for expenses, replenishments, settlements.
  - Files: `packages/filament-petty-cash-ir/src/Application/UseCases/*`.
- Infrastructure
  - Accounting adapter (IR) handles journal entry posting/reversal.
  - Audit logger persists `PettyCashAuditEvent` records.
  - Idempotency service tracks action keys.
  - AI provider interface + suggestion log (`petty_cash_ai_suggestions`).
  - Controls service records exceptions for threshold/duplicate/anomaly checks.
  - Files: `packages/filament-petty-cash-ir/src/Infrastructure/*`.
- UI/API
  - Filament resources and API controllers call `PettyCashPostingService`.
  - Files: `packages/filament-petty-cash-ir/src/Filament/*`, `packages/filament-petty-cash-ir/src/Http/Controllers/Api/*`.

## Key decisions
- Compatibility: Keep existing service API (`PettyCashPostingService`) and routes intact; implement delegation internally.
- Idempotency: Introduce `petty_cash_action_logs` with unique keys per action + subject to prevent double posting.
- Audit logging: Centralized logger writes to `PettyCashAuditEvent` for critical transitions.
- Accounting integration: Adapter pattern (`AccountingAdapterInterface`) isolates accounting IR logic and reversal support.

## Core invariants (examples)
- Expense must be `approved` before `paid`.
- Receipt is required when `receipt_required=true`.
- Replenishment must be `approved` before `paid`.
- Settlement must be `approved` before `posted`.

## Transactions & idempotency
- Money‑critical operations (post/reverse) run inside database transactions.
- Idempotency keys are optional but supported; repeated calls with same key are no‑op.

## Extensibility
- Workflow engine, controls, AI provider, and reporting plug into application layer without changing controllers.
