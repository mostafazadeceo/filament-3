<?php

return [
    'timezone' => 'Asia/Tehran',

    'date' => [
        'calendar' => 'jalali',
        'use_persian_digits' => true,
    ],

    'attendance' => [
        'late_grace_minutes' => 0,
        'rounding_minutes' => 1,
        'allow_manual_punch' => true,
        'allow_employee_self_punch' => true,
        'require_approval' => true,
        'night_hours' => [
            'start' => '22:00',
            'end' => '06:00',
        ],
    ],

    'payroll' => [
        'official_slip_type' => 'official',
        'internal_slip_type' => 'internal',
        'lock_after_post' => true,
    ],

    'api' => [
        'prefix' => 'api/v1/payroll-attendance',
        'rate_limit' => '60,1',
        'tenant_header' => 'X-Tenant-ID',
    ],
];
