<?php

return [
    'vapid_public_key' => null,
    'vapid_subject' => env('VAPID_SUBJECT'),
    'auto_generate_vapid' => true,
    'subscribe_endpoint' => '/filament-notify/webpush/subscribe',
    'service_worker_path' => '/filament-notify-sw.js',
];
