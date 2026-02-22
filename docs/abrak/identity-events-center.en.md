# Abrak Hub as the Identity and Events Center

## Role
- central SSO authority
- source of truth for entitlements/plans/licenses
- intake and processing of service usage events
- central command issuer for suspend/activate/plan change

## Linked contracts
- repository manifest: `.abrak/manifest.yml`
- service contract: `.abrak/service-contract.yml`
- central registry reference: `https://github.com/mostafazadeceo/abrak-co-dataenter-v1/tree/main/registry`

## Safety constraints
- no execution changes on `PC.HAIDA.CO` and `CRM.HAIDA.CO`
- all changes require ADR + rollback path
