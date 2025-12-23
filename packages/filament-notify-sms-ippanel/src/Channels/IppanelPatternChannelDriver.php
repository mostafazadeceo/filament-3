<?php

namespace Haida\FilamentNotify\SmsIppanel\Channels;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Support\Facades\Http;

class IppanelPatternChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'sms_ippanel';
    }

    public function label(): string
    {
        return 'SMS (IPPanel)';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            TextInput::make('api_key')
                ->label('توکن API')
                ->password()
                ->helperText('توکن حساب IPPanel'),
            TextInput::make('base_url')
                ->label('Base URL')
                ->placeholder('https://edge.ippanel.com/v1')
                ->helperText('آدرس پایه API (پیشنهادی: https://edge.ippanel.com/v1).'),
            TextInput::make('from_number')
                ->label('شماره فرستنده')
                ->helperText('در صورت خالی بودن، از تنظیمات قالب استفاده می‌شود.'),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $recipient = $context->recipient['phone'] ?? null;
        if (! $recipient && isset($context->recipient['notifiable'])) {
            $recipient = $context->recipient['notifiable']->phone ?? $context->recipient['notifiable']->mobile ?? null;
        }

        if (! $recipient) {
            return DeliveryResult::failure('missing_phone');
        }

        $meta = $message->meta;
        $patternCode = $meta['pattern_code'] ?? $meta['code'] ?? null;
        $fromNumber = $meta['from_number'] ?? $context->channelSettings['from_number'] ?? config('filament-notify-sms-ippanel.from_number');

        $apiKey = $context->channelSettings['api_key'] ?? config('filament-notify-sms-ippanel.api_key');
        $baseUrl = $this->normalizeBaseUrl($context->channelSettings['base_url'] ?? config('filament-notify-sms-ippanel.base_url'));

        if (! $apiKey || ! $baseUrl) {
            return DeliveryResult::failure('missing_api_credentials');
        }

        if (! $fromNumber) {
            return DeliveryResult::failure('missing_from_number');
        }

        if ($patternCode) {
            $paramMap = $meta['param_map'] ?? [];
            $params = [];
            if (is_array($paramMap)) {
                foreach ($paramMap as $key => $path) {
                    $params[$key] = data_get($context->context, $path);
                }
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post($baseUrl . '/api/send', [
                'sending_type' => 'pattern',
                'from_number' => $fromNumber,
                'code' => $patternCode,
                'recipients' => [$recipient],
                'params' => $params,
            ]);

            if ($response->successful()) {
                return DeliveryResult::success($response->json());
            }

            return DeliveryResult::failure('ippanel_error', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
        }

        $body = trim((string) $message->body);
        if ($body === '' && ! empty($message->subject)) {
            $body = trim((string) $message->subject);
        }

        if ($body === '') {
            return DeliveryResult::failure('missing_sms_body');
        }

        $payload = [
            'sending_type' => 'webservice',
            'from_number' => $fromNumber,
            'message' => $body,
            'params' => [
                'recipients' => [$recipient],
            ],
        ];

        if (! empty($meta['send_time'])) {
            $payload['send_time'] = $meta['send_time'];
        }

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->post($baseUrl . '/api/send', $payload);

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('ippanel_error', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);
    }

    public function connectionTestForm(): array
    {
        return [];
    }

    public function sendTestForm(): array
    {
        return [
            TextInput::make('phone')
                ->label('شماره گیرنده')
                ->required(),
            TextInput::make('pattern_code')
                ->label('کد پترن')
                ->helperText('اگر خالی باشد پیام معمولی ارسال می‌شود.'),
            TextInput::make('from_number')
                ->label('شماره فرستنده')
                ->helperText('اختیاری. اگر خالی باشد از تنظیمات کانال استفاده می‌شود.'),
            Textarea::make('message')
                ->label('متن پیام')
                ->rows(3)
                ->helperText('برای ارسال پیام معمولی استفاده می‌شود.')
                ->columnSpanFull(),
            Textarea::make('params')
                ->label('پارامترها (JSON)')
                ->default('{}')
                ->rows(4)
                ->rules(['nullable', 'json'])
                ->columnSpanFull(),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $apiKey = $settings['api_key'] ?? config('filament-notify-sms-ippanel.api_key');
        $baseUrl = $this->normalizeBaseUrl($settings['base_url'] ?? config('filament-notify-sms-ippanel.base_url'));

        if (! $apiKey || ! $baseUrl) {
            return DeliveryResult::failure('توکن یا آدرس API ناقص است.');
        }

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->post($baseUrl . '/api/acl/auth/check_token');

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('ippanel_connection_error', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $phone = $data['phone'] ?? null;
        if (! $phone) {
            return DeliveryResult::failure('شماره گیرنده وارد نشده است.');
        }

        $patternCode = $data['pattern_code'] ?? null;

        $panelId = (string) data_get($context, 'panel.id', 'admin');
        $deliveryContext = ChannelTestContextFactory::make(
            $panelId,
            $this->key(),
            $settings,
            ['phone' => $phone],
            $context,
        );

        if ($patternCode) {
            $params = $data['params'] ?? [];
            if (is_string($params)) {
                $decoded = json_decode($params, true);
                if (! is_array($decoded)) {
                    return DeliveryResult::failure('پارامترهای JSON معتبر نیستند.');
                }
                $params = $decoded;
            }

            if (! is_array($params)) {
                return DeliveryResult::failure('پارامترهای JSON معتبر نیستند.');
            }

            $paramMap = [];
            foreach ($params as $key => $value) {
                $paramMap[$key] = 'test.' . $key;
            }

            $context['test'] = $params;

            $message = new RenderedMessage(
                subject: null,
                body: '',
                meta: [
                    'pattern_code' => $patternCode,
                    'from_number' => $data['from_number'] ?? null,
                    'param_map' => $paramMap,
                ],
            );

            return $this->send($deliveryContext, $message);
        }

        $body = trim((string) ($data['message'] ?? ''));
        if ($body === '') {
            return DeliveryResult::failure('missing_sms_body');
        }

        $message = new RenderedMessage(
            subject: null,
            body: $body,
            meta: [
                'from_number' => $data['from_number'] ?? null,
            ],
        );

        return $this->send($deliveryContext, $message);
    }

    protected function normalizeBaseUrl(?string $baseUrl): string
    {
        $baseUrl = trim((string) $baseUrl);

        if ($baseUrl === '') {
            $baseUrl = (string) config('filament-notify-sms-ippanel.base_url', 'https://edge.ippanel.com/v1');
        }

        if (str_contains($baseUrl, 'ippanel.com') && ! str_contains($baseUrl, 'edge.ippanel.com')) {
            return 'https://edge.ippanel.com/v1';
        }

        $baseUrl = rtrim($baseUrl, '/');

        if (str_contains($baseUrl, 'edge.ippanel.com') && ! str_contains($baseUrl, '/v1')) {
            return $baseUrl . '/v1';
        }

        return $baseUrl;
    }
}
