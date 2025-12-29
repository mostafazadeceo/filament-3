<?php

return [
    'table_prefix' => 'petty_cash_',

    'api' => [
        'enabled' => true,
        'rate_limit' => '60,1',
    ],

    'workflow' => [
        'require_attachments' => true,
        'auto_submit_on_create' => false,
        'allow_edit_after_submit' => false,
    ],

    'alerts' => [
        'threshold_enabled' => true,
    ],
];
