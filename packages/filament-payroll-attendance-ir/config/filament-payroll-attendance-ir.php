<?php

return [
    'table_prefix' => 'payroll_',

    'api' => [
        'enabled' => true,
        'rate_limit' => '60,1',
    ],

    'attendance' => [
        'default_daily_hours' => 8,
        'default_weekly_hours' => 44,
        'default_monthly_hours' => 176,
        'late_grace_minutes' => 0,
        'night_start' => '22:00',
        'night_end' => '06:00',
    ],

    'payroll' => [
        'overtime_factor' => 1.4,
        'night_factor' => 0.35,
        'friday_factor' => 0.4,
        'holiday_factor' => 0.4,
        'flat_allowance_tax_rate' => 0.1,
    ],

    'locks' => [
        'prevent_edit_after_post' => true,
    ],

    'privacy' => [
        'location_tracking_enabled' => true,
        'location_retention_days' => 30,
        'biometric_enabled' => false,
        'biometric_retention_days' => 7,
        'require_access_reason' => false,
    ],

    'ai' => [
        'enabled' => false,
        'provider' => \Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai\FakeAiProvider::class,
        'log_payloads' => false,
    ],

    'policy' => [
        'default_rules' => [
            'require_geofence' => false,
            'require_wifi' => false,
            'require_device_ref' => false,
            'manual_edit_requires_reason' => true,
            'max_overtime_minutes' => null,
            'min_event_interval_minutes' => null,
            'max_travel_speed_kmh' => null,
            'late_grace_minutes' => null,
            'shift_end_grace_minutes' => null,
            'break_deduction_minutes' => null,
            'remote_only_if_branch' => false,
            'leave_accrual_multiplier' => null,
            'leave_accrual_overrides' => null,
        ],
    ],

    'fraud' => [
        'min_event_interval_minutes' => 2,
        'max_travel_speed_kmh' => 120,
    ],

    'exceptions' => [
        'default_severity' => 'low',
        'require_resolution_notes' => true,
        'default_assignee_role' => 'hr_manager',
    ],
];
