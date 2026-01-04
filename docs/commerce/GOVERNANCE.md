# Commerce Governance

## Scope
- Applies to commerce core, payments, POS, storefront builder, experience, and marketplace connectors.
- Covers security, privacy, compliance workflow, and operational controls.

## Data protection
- No raw card data storage; only gateway tokens and references are persisted.
- Secrets (API keys, webhook secrets) are encrypted at rest.
- Logs must be sanitized to remove Authorization headers and payment secrets.

## Tenancy and access control
- Every query and API endpoint is tenant-scoped.
- All UI and API endpoints require IAM permission checks.
- Permissions are registered in the CapabilityRegistry with Persian labels.

## Consent and messaging
- Marketing communications require explicit opt-in.
- CSAT/NPS and transactional messages are allowed; marketing is opt-in only.

## Audit and traceability
- Sensitive actions (refunds, discounts, inventory adjustments, cash drops) are auditable.
- Compliance exceptions and fraud rules are auditable records.

## Compliance workflow
- Exceptions are created via rules and reviewed in the compliance inbox.
- Resolution requires a user action, recorded with timestamp and resolver.
- Weekly digest notifications are sent via notify-core.

## Data retention
- Tenant-configurable retention for audit and compliance records.
- Default recommendation: 24 months for audit logs, 12 months for exceptions.

## Non-surveillance guarantee
- No covert monitoring, behavior surveillance, or hidden recording is permitted.
- Analytics are limited to product and business events with consent where required.
