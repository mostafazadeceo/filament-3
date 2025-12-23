<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'default_chat_id' => env('TELEGRAM_DEFAULT_CHAT_ID'),
    'base_url' => env('TELEGRAM_BASE_URL', 'https://api.telegram.org'),
];
