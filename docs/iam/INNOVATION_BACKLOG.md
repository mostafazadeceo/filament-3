# IAM Innovation Backlog

Legend: [Impl] = implementation area, [Test] = test coverage target

## Core Access + Governance
1) Deny-by-default checks in `IamAuthorization` with tenant-aware fallback. [Impl] Support/IamAuthorization.php [Test] PermissionResolutionTest
2) Capability registry sync gating with cooldown + audit. [Impl] Services/CapabilitySyncService.php [Test] CapabilitySyncTest
3) Reason-required role/permission changes. [Impl] Filament resources + services [Test] RolePermissionAuditTest
4) Permission template application audit trail. [Impl] PermissionTemplateResource [Test] PermissionTemplateAuditTest
5) Delegated admin scope enforcement on queries. [Impl] Services/DelegatedAdminService.php [Test] DelegatedAdminScopeTest
6) Tenant policy gating (company allowed permissions). [Impl] AccessService.php [Test] TenantPolicyGateTest
7) Subscription gate integration into access checks. [Impl] AccessService.php [Test] SubscriptionGateTest
8) Bulk permission export with audit. [Impl] PermissionResource actions [Test] PermissionExportAuditTest

## User Lifecycle
9) Tenant-scoped invitations with expiry + token. [Impl] Models/IamInvitation [Test] InvitationFlowTest
10) Activation flow that sets membership status + joined_at. [Impl] InviteUserService [Test] InvitationActivationTest
11) Suspension with reason + audit. [Impl] UserLifecycleService [Test] SuspensionAuditTest
12) Re-activation flow with step-up. [Impl] ProtectedActionService [Test] ReactivationStepUpTest
13) Membership status governance (invited/active/inactive). [Impl] TenantUser updates [Test] TenantMembershipStatusTest

## PAM / JIT (Privileged Access)
14) Privileged role eligibility assignments. [Impl] Models/PrivilegeEligibility [Test] EligibilityAssignmentTest
15) Elevation request with reason + TTL. [Impl] PrivilegeElevationService [Test] ElevationRequestTest
16) Optional approval workflow. [Impl] PrivilegeApprovalService [Test] ElevationApprovalTest
17) Activation records with expiry. [Impl] Models/PrivilegeActivation [Test] ActivationExpiryTest
18) Auto-expire job removes role. [Impl] Jobs/ExpirePrivilegeActivations [Test] ActivationRevokeTest
19) Weekly digest for expiring activations. [Impl] NotificationService + command [Test] DigestNotificationTest
20) High-privilege role catalog. [Impl] Config + UI [Test] HighPrivilegeCatalogTest

## Impersonation
21) Impersonation session model with TTL + token. [Impl] Models/ImpersonationSession [Test] ImpersonationStartStopTest
22) Signed token verification. [Impl] Services/ImpersonationService [Test] ImpersonationTokenTest
23) Reason + ticket id required. [Impl] UI/API validation [Test] ImpersonationReasonTest
24) Restricted mode (read-only) by default. [Impl] Middleware/ImpersonationGuard [Test] ImpersonationRestrictedTest
25) Step-up required for write actions. [Impl] ProtectedActionService [Test] ImpersonationStepUpTest
26) Tenant owner notification (configurable). [Impl] NotificationService [Test] ImpersonationNotifyTest

## Sessions + Protected Actions
27) User session tracking table. [Impl] Models/UserSession [Test] SessionCreatedOnLoginTest
28) Session revoke + forced logout. [Impl] SessionService [Test] SessionRevokeTest
29) Login history retention policy. [Impl] SessionService cleanup [Test] SessionRetentionTest
30) Protected actions step-up token. [Impl] ProtectedActionService [Test] ProtectedActionTokenTest
31) MFA-required actions list. [Impl] Config + guards [Test] ProtectedActionMfaTest
32) Session ID regen on privilege changes. [Impl] Service hooks [Test] SessionRegenTest

## MFA
33) TOTP setup + verification. [Impl] MfaService (google2fa) [Test] TotpEnrollVerifyTest
34) Backup codes rotation. [Impl] MfaService [Test] BackupCodesTest
35) MFA reset with reason + audit. [Impl] MfaService + UI [Test] MfaResetAuditTest
36) WebAuthn scaffold (disabled). [Impl] MfaWebAuthnAdapter [Test] WebAuthnScaffoldTest

## SSO + SCIM
37) OIDC connector scaffold. [Impl] Services/Sso/OidcAdapter [Test] OidcConfigTest
38) SAML connector scaffold. [Impl] Services/Sso/SamlAdapter [Test] SamlConfigTest
39) SCIM Users endpoint (list/create/update/delete). [Impl] Http/Controllers/Scim [Test] ScimUserScopeTest
40) SCIM Groups endpoint. [Impl] Http/Controllers/Scim [Test] ScimGroupScopeTest
41) SCIM auth + tenant resolution. [Impl] Middleware/ScimAuth [Test] ScimAuthTest

## API + Docs
42) /api/v1/iam base routes for enterprise features. [Impl] routes/api.php [Test] ApiScopeTest
43) OpenAPI spec updated for IAM endpoints. [Impl] docs/iam/API.md + openapi.yaml [Test] ApiDocsSmokeTest
44) Rate limit override per feature. [Impl] config/filamat-iam.php [Test] RateLimitConfigTest

## UI + UX (Filament)
45) Access requests resource. [Impl] Filament/Resources/AccessRequestResource [Test] AccessRequestUiTest
46) PAM eligibility + activation resources. [Impl] Filament/Resources/Privilege* [Test] PrivilegeUiTest
47) Session management resource. [Impl] Filament/Resources/UserSessionResource [Test] SessionUiTest
48) MFA management page. [Impl] Filament/Pages/MfaSettings [Test] MfaUiTest
49) Impersonation banner + stop action. [Impl] Filament widgets + middleware [Test] BannerVisibleTest

## Audit + Security
50) Audit events for privileged actions. [Impl] AuditService usage [Test] PrivilegedAuditTest
51) Security events for failed MFA/step-up. [Impl] SecurityEventService [Test] SecurityEventTest
52) Hash-chain continuation for new audit events. [Impl] AuditHashService [Test] AuditHashChainTest

