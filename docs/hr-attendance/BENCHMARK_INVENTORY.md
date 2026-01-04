# HR Attendance Benchmark Inventory (M1)

This inventory consolidates competitor best practices (Zonix, Finto, NanoWatch, BambooHR, Zoho People) and maps them to a privacy-first, opt-in, user-initiated attendance architecture. No surveillance features are allowed; all sensitive data is consent-gated and audit-logged.

## Attendance capture methods
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Mobile GPS check-in/out (consent, windowed) | Accurate proof for remote/on-site | Privacy sensitivity, spoofing | Domain: `TimeEvent`; App: `ClockIn`/`ClockOut`; Infra: `MobileDriver` + `GeoFenceProof`; Privacy: `PrivacyEnforcer`; API: `/api/v1/payroll-attendance/punches` + planned `/time-events` |
| WiFi SSID verification | Low-friction in-branch validation | SSID spoofing | Infra: `WifiSSIDProof`; Policy: `AttendancePolicy.rules.require_wifi` + `AttendancePolicyEngine` |
| Web browser check-in | Easy for office users | IP spoofing | Infra: `WebDriver`; Policy: `AttendancePolicy.rules.require_device_ref` + network allowlist (config) |
| Kiosk mode (tablet) | High-throughput on-site | Shared device abuse | Infra: `KioskDriver` + device binding metadata + session timeout |
| QR code check-in | Fast branch validation | QR leakage | Infra: kiosk driver + rotating tokens (future) |
| Selfie capture on check-in (opt-in) | Stronger identity proof | Biometric sensitivity | Infra: `BiometricVerificationInterface` + `FaceVerificationDriver` (stub, opt-in) |
| Offline punch queue | Continuity in low-connectivity | Replay risk | Driver-level offline queue + idempotent event ingestion (future) |
| Hardware device event ingestion | Enterprise integrations | Device trust | Infra: `HardwareDeviceDriver` + signed events endpoint (future) |

## Shift and schedule engine
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Fixed shift templates | Standardize schedules | Misfit for flex teams | Model: `PayrollAttendanceShift`; Resource: `PayrollAttendanceShiftResource` |
| Rotating shifts | 24/7 operations | Complexity | Domain: `ShiftPattern` + rotation rules (future UI) |
| Split shifts | Multi-peak operations | Overlap errors | Domain: `ShiftPattern` + segments (future) |
| Break rules | Consistent deduction | Disputes | `AttendanceCalculatorService` + policy rule `break_deduction_minutes` |
| Grace periods | Reduce false lateness | Abuse risk | Policy: `late_grace_minutes` + `AttendanceCalculatorService` |
| Rounding rules | Payroll alignment | Disputes | Policy: `rounding` (future) + `RecalculateWorktime` |
| Flexible core hours | Knowledge-worker support | Enforcement ambiguity | Domain: `WorkCalendar` + core hours (future) |
| Branch calendars | Multi-branch schedules | Drift from policy | Models: `WorkCalendar`, `HolidayRule` (future UI) |

## Leave, mission, overtime
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Leave accrual policies | Compliance and fairness | Policy complexity | Service: `LeaveAccrualService`; Models: `PayrollLeaveType`, `PayrollLeaveRequest` |
| Leave balance ledger | Auditability | Reconciliation overhead | Use `Timesheet` + audit events (future) |
| Partial-day leave | Real-world accuracy | Edge cases | `PayrollLeaveRequest` with hour-based fields (future) |
| Sick leave with documents | Compliance | Sensitive data | Secure uploads + consent + audit log (future) |
| Mission/travel requests | Track off-site work | Abuse | Model: `PayrollMission` + `MissionRequest` (UI/API missing) |
| Overtime request workflow | Pre-approval control | Bottlenecks | Domain: `OvertimeRequest` (UI/API missing) |
| Comp-off (time-off in lieu) | Reduce cash overtime | Tracking complexity | Policy + leave balance mapping (future) |
| Blackout dates | Business continuity | Employee friction | Policy rules + UI warnings (future) |

## Approvals and notifications
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Multi-step approvals | Strong governance | Delay | `RequestStateMachine` + `ApprovalStateMachine` (needs wiring) |
| Delegation / backup approver | Operational continuity | Misuse | Role + policy extension (future) |
| SLA reminders | Reduce backlog | Notification fatigue | Use `haida/filament-notify-core` TriggerDispatcher (to be integrated) |
| Escalations | Enforce compliance | Over-escalation | `RaiseException` + `AttendanceException` + assignee resolver |
| Exception inbox | Centralized review | Overload | Resource: `PayrollAttendanceExceptionResource` |
| Manager justification | Audit-ready edits | Slower workflow | Policy: `manual_edit_requires_reason` + `AntiFraudDetector` + audit log |

## Reporting and exports
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Timesheet summary | Fast monthly review | Data accuracy | `GenerateTimesheets` + `AttendanceReportService` |
| Tardiness report | Operational insight | Fairness concerns | `AttendanceReportService` filters + policy context |
| Overtime cost report | Payroll control | Misinterpretation | Payroll reports + rate context (future) |
| Leave balance report | HR planning | Data drift | `LeaveAccrualService` + `PayrollLeaveRequest` (future) |
| Staffing coverage gaps | Ops planning | False positives | `AttendanceReportService::coverageGapReport` + schedules |
| Export CSV/XLSX | Integrations | Data leakage | Permission-gated `ExportReports` + audit log (future) |

## Admin, security, multi-tenant
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Per-tenant policies | Tenant autonomy | Misconfiguration | `AttendancePolicy` scoped by tenant/company/branch |
| Manager scope limits | Least privilege | Hierarchy complexity | IAM scopes + org models `Department`/`Position` |
| Branch-level access | Operational control | Edge-case leaks | `UsesTenant` + team permissions + policies |
| Subscription gating | Monetization | Feature confusion | IAM subscription enforcement (built-in) |

## Privacy and compliance
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Explicit consent capture | Legal compliance | Low adoption | `EmployeeConsent` + UI/API (future) |
| Location windowing | Minimize data | Enforcement gaps | `PrivacyEnforcer` only keeps location for clock-in/out |
| Biometric opt-in | Trust | Regulatory burden | `filament-payroll-attendance-ir.privacy.biometric_enabled` + consent |
| Access audit log | Accountability | Log volume | `SensitiveAccessLog` + `LogsSensitiveAccess` trait |

## Integrations and hardware
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Accounting posting | End-to-end payroll | Reconciliation | `ClosePayrollPeriod` -> accounting adapter (future) |
| Webhooks | External workflows | Security | `PayrollWebhookService` + `SendPayrollWebhookJob` |
| Device vendor adapters | Enterprise adoption | Vendor lock-in | Driver interface + adapter registry (future) |
| SSO / IdP sync | Reduced admin | Identity drift | IAM sync adapter + tenant config (future) |

## AI-assist (safe and opt-in)
| Feature | Value | Risk | Implementation in our architecture |
| --- | --- | --- | --- |
| Compliance summary | Faster audits | Hallucinations | `AiReportService` + `AiProviderInterface` + `FakeAiProvider` fallback |
| Policy tuning hints | Better rules | Over-automation | Suggestion-only + audit log in `PayrollAiLog` |
| Anomaly explanations | Clarity | Bias | Use aggregates only (no profiling) |
| Manager narrative report | Executive clarity | Misuse | `AttendanceManagementReportsPage` + permission `payroll.ai.use` |
