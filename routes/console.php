<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-mail', function () {
    $token = env('MAILTRAP_API_TOKEN');
    if (! $token) {
        $this->error('MAILTRAP_API_TOKEN تنظیم نشده است.');
        return self::FAILURE;
    }

    $payload = [
        'from' => [
            'email' => env('MAILTRAP_FROM_ADDRESS', 'hello@demomailtrap.co'),
            'name' => env('MAILTRAP_FROM_NAME', 'Mailtrap Test'),
        ],
        'to' => [
            ['email' => 'dr.mostafazade@gmail.com'],
        ],
        'subject' => 'Mailtrap API test',
        'text' => 'تست ارسال ایمیل با Mailtrap API',
    ];

    $response = Http::withToken($token)
        ->acceptJson()
        ->asJson()
        ->post('https://send.api.mailtrap.io/api/send', $payload);

    if ($response->successful()) {
        $this->info('ایمیل با موفقیت ارسال شد.');
        $this->line(json_encode($response->json(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return self::SUCCESS;
    }

    $this->error('ارسال ایمیل ناموفق بود.');
    $this->line('HTTP ' . $response->status());
    $this->line((string) $response->body());

    return self::FAILURE;
})->purpose('Send Mailtrap API test email');
