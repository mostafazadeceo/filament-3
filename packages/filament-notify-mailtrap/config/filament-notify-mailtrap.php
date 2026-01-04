<?php

declare(strict_types=1);

return [
    'base_url' => env('MAILTRAP_SEND_BASE_URL', 'https://send.api.mailtrap.io/api'),
    'api_token' => env('MAILTRAP_SEND_API_TOKEN'),
    'default_from_address' => env('MAILTRAP_FROM_ADDRESS'),
    'default_from_name' => env('MAILTRAP_FROM_NAME'),
    'default_category' => env('MAILTRAP_SEND_CATEGORY', 'Filament Notify'),
];
