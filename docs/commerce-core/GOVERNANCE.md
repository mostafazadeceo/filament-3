# Commerce Core Governance

## Security
- All changes are tenant-scoped and audited where configured.
- Sensitive actions should be wrapped in transactions.

## Compliance
- Exceptions are created via CommerceComplianceService.
- Fraud rules are managed by authorized operators only.
- Weekly digests are emitted via the `commerce:compliance-digest` command.

## Data integrity
- Enforce unique constraints for tenant-level identifiers.
- Maintain indexes on tenant_id and status for scale.

## Privacy
- No behavioral surveillance is implemented.
- Analytics are limited to product and business events.
