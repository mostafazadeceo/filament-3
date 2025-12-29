<?php

return [
    'table_prefix' => 'workhub_',

    'api' => [
        'rate_limit' => '60,1',
    ],

    'workflow' => [
        'default_name' => 'گردش‌کار پیش‌فرض',
        'default_statuses' => [
            ['name' => 'کارهای جدید', 'slug' => 'todo', 'category' => 'todo', 'color' => '#f59e0b', 'sort_order' => 10],
            ['name' => 'در حال انجام', 'slug' => 'in-progress', 'category' => 'in_progress', 'color' => '#3b82f6', 'sort_order' => 20],
            ['name' => 'انجام شده', 'slug' => 'done', 'category' => 'done', 'color' => '#10b981', 'sort_order' => 30],
        ],
    ],

    'work_item' => [
        'priorities' => [
            'low' => 'کم',
            'medium' => 'متوسط',
            'high' => 'بالا',
            'urgent' => 'فوری',
        ],
    ],
];
