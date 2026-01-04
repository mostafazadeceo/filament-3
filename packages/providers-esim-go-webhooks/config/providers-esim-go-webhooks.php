<?php

declare(strict_types=1);

return [
    'route_prefix' => 'api/v1/providers/esim-go',
    'callback_path' => 'callback',
    'rate_limit' => env('ESIM_GO_WEBHOOK_RATE_LIMIT', '30,1'),
];
