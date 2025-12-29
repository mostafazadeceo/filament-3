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
];
