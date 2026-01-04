# Upgrade Roadmap

## Phase 0 — Baseline & legacy parity scan (done)
- Output: `UPGRADE_BASELINE.md`, `LEGACY_INVENTORY.md`, `LEGACY_TO_MODERN_MAPPING.md`.
- Gate: baseline docs reference real files.

## Phase 1 — Clean architecture foundation (M4)
- Deliverables:
  - Domain enums + invariant guardrails.
  - Application use‑cases (submit/approve/post/reverse).
  - Infrastructure interfaces (accounting, attachments, audit).
  - Idempotency tracking.
- Gate:
  - Existing flows still work with wrappers.
  - Idempotent post/reverse with audit logging.

## Phase 2 — Controls + workflow engine (M5)
- Deliverables:
  - Workflow rules + step tracking.
  - Control rules + exceptions model + inbox UI.
  - Reconciliation + cash count flows.
  - Reporting dashboards + exports.
- Gate:
  - New UI pages are permission‑gated.
  - Exceptions created for rule breaches.

## Phase 3 — AI continuous auditor (M6)
- Deliverables:
  - AI provider interface + fake provider.
  - Opt‑in config + audit logs.
  - Suggestions UI on expense create/edit.
- Gate:
  - AI disabled by default; no calls when disabled.
  - Suggestions logged on accept/reject.

## Phase 4 — Tests, scenario runner, docs, OpenAPI (M7)
- Deliverables:
  - Tests for tenancy, permissions, state machine, idempotency.
  - Scenario runner updated (idempotent).
  - Docs: SPEC/API/AI_GOVERNANCE/UPGRADE_NOTES.
  - OpenAPI updated for new endpoints.
- Gate:
  - `php artisan test` passes.
  - Scenario runner completes safely.

## Risk mitigation
- Use feature flags for large features (workflow/AI/controls).
- Keep legacy API paths intact.
- Use backward‑compatible columns and migrations.
