<?php

return [
    'token' => env('WHATSAPP_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'base_url' => env('WHATSAPP_BASE_URL', 'https://graph.facebook.com/v17.0'),
];
