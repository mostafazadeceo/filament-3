# Privacy & Compliance Governance (M3)

## Principles
- No surveillance: only user-initiated check-in/out events, no continuous tracking.
- Consent-first: explicit opt-in for location and biometrics.
- Data minimization: collect only what is required for attendance verification.
- Auditability: log access to sensitive records with reason.

## Sensitive data categories
- PII: employee identity, contact details.
- Attendance: time events, punches, timesheets.
- Location proof: GPS, WiFi SSID, IP (only during check-in/out).
- Biometrics: optional, opt-in, no raw video or continuous streams.

## Consent model
Storage: `payroll_employee_consents`
- `consent_type` examples: `location_tracking`, `biometric_verification`.
- Per-employee and tenant scoped.
- Capture who granted consent + timestamps for grant/revoke.

## Location tracking rules
Enforcement:
- Only during check-in/out (`TimeEventType::ClockIn|ClockOut`).
- Requires explicit consent.
- Can be disabled per tenant via config.
Implementation:
- `PrivacyEnforcer::sanitizeTimeEventPayload()` redacts location fields when consent/rules are not satisfied.
- Config: `filament-payroll-attendance-ir.privacy.location_tracking_enabled`.

## Biometric rules (optional)
Enforcement:
- Disabled by default.
- Requires explicit consent.
- Never store raw video or continuous streams.
Implementation:
- `BiometricVerificationInterface` + `FaceVerificationDriver` (stub, opt-in).
- Config: `filament-payroll-attendance-ir.privacy.biometric_enabled`.

## Access control and audit
Requirement:
- Log who accessed sensitive data and why.
Implementation:
- API logging: `ApiController::logSensitiveAccess()` writes to `payroll_sensitive_access_logs`.
- UI logging: sensitive Filament pages use `LogsSensitiveAccess` (resource concern).
- Reason capture:
  - API: `X-Access-Reason` header.
  - UI: `?access_reason=...` query param (enforced when configured).
- Config: `filament-payroll-attendance-ir.privacy.require_access_reason`.

## Retention
Default settings (configurable):
- Location artifacts: 30 days.
- Biometric verification artifacts: 7 days.
Configs:
- `filament-payroll-attendance-ir.privacy.location_retention_days`
- `filament-payroll-attendance-ir.privacy.biometric_retention_days`

## AI audit logging
- AI usage is opt-in and permission-gated (`payroll.ai.use`).
- Logs store minimal metadata by default in `payroll_ai_logs`.
- Raw inputs/outputs are only stored when `filament-payroll-attendance-ir.ai.log_payloads` is enabled.

## Data integrity and transparency
- Sensitive access is audit-logged with reason.
- Attendance events are immutable in intent; corrections require approvals and create audit exceptions.
- Employee-facing UI must communicate consent states and tracking scope.

## Implementation references
- Privacy enforcement: `packages/filament-payroll-attendance-ir/src/Application/Services/PrivacyEnforcer.php`.
- Policy engine: `packages/filament-payroll-attendance-ir/src/Application/Services/AttendancePolicyEngine.php`.
- Sensitive access logs: `packages/filament-payroll-attendance-ir/src/Application/Services/SensitiveAccessLogger.php`.
- Consent model: `packages/filament-payroll-attendance-ir/src/Domain/Models/EmployeeConsent.php`.
- Access logs model: `packages/filament-payroll-attendance-ir/src/Domain/Models/SensitiveAccessLog.php`.
- API hooks: `packages/filament-payroll-attendance-ir/src/Http/Controllers/Api/V1/ApiController.php`.
- Filament hooks: `packages/filament-payroll-attendance-ir/src/Filament/Resources/Concerns/LogsSensitiveAccess.php`.
