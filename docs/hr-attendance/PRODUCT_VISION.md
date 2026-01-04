# HR Attendance Product Vision (M1)

## Vision
Deliver the most trusted, enterprise-grade HR attendance system for Iran: privacy-first, compliant by default, and extensible through clean architecture and driver-based integrations. Every feature is opt-in, user-initiated, and auditable.

## What makes it best-in-class in Iran
- Legal alignment with Iranian labor, insurance, and tax rules through effective-dated compliance tables.
- Jalali-aware UX and localized workflows for HR, managers, and employees.
- Multi-tenant, multi-branch scale with strict IAM controls and subscription gating.
- Offline-capable capture paths with deterministic sync and audit trails.
- Safe hardware integrations without surveillance patterns or continuous tracking.

## Differentiators vs competitors
Zonix / Finto / NanoWatch
- Similar capture methods, but we enforce explicit consent, windowed location collection, and access-reason audit logs.
BambooHR / Zoho People
- Match approval workflows and mobile readiness while emphasizing privacy-by-design and tenant isolation.

## Principles
1) No surveillance: only user-initiated check-ins/outs; no continuous tracking.
2) Consent and transparency: visible consent records, short retention, and clear scope.
3) Auditability: immutable logs for sensitive operations and data access.
4) Extensibility: clean layers with driver interfaces for devices and proof methods.
5) Enterprise readiness: robust policies, exceptions, and exportable reporting.

## Target experience
HR managers
- Configure policies, approve exceptions, and review audit-ready reports.
Line managers
- See team attendance and handle approvals with minimal friction.
Employees
- Simple check-in/out, transparent proofs, and clear leave balances.

## Architecture alignment
- Domain: `EmployeeProfile`, `AttendancePolicy`, `ShiftPattern`, `TimeEvent`, `Timesheet`, `LeaveRequest`, `AttendanceException`, `AuditEvent`.
- Application: `ClockIn/ClockOut`, `RequestLeave`, `ApproveLeave`, `GenerateTimesheets`, `RaiseException`.
- Infrastructure: drivers for mobile/web/kiosk/hardware and proofs (geo-fence, WiFi), plus optional biometrics.
- UI: Filament resources and dashboards registered through `FilamentPayrollAttendanceIrPlugin`.
- API: `/api/v1/payroll-attendance/*` (existing) with IAM scopes and tenant middleware; optional alias `/api/v1/hr-attendance/*` can be introduced without breaking clients.

## Privacy translation of competitor best practices
- Location and photo proofs are allowed only during check-in/out windows.
- Biometric verification is optional, per-tenant and per-employee, with short retention.
- Managers must provide a reason to view or override sensitive data when required by config.
- AI assist is suggestion-only, opt-in, and focused on anomalies and compliance signals.
