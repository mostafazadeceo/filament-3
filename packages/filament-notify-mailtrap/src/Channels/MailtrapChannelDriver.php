<?php

declare(strict_types=1);

namespace Haida\FilamentNotify\Mailtrap\Channels;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MailtrapChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'mailtrap';
    }

    public function label(): string
    {
        return 'Mailtrap';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            TextInput::make('api_token')
                ->label('توکن ارسال Mailtrap')
                ->password()
                ->revealable()
                ->helperText('توکن Send API را از Mailtrap دریافت کنید.'),
            TextInput::make('from_address')
                ->label('آدرس فرستنده')
                ->email()
                ->helperText('در صورت خالی بودن از تنظیمات اصلی استفاده می‌شود.'),
            TextInput::make('from_name')
                ->label('نام فرستنده')
                ->helperText('اختیاری.'),
            TextInput::make('category')
                ->label('دسته‌بندی ارسال')
                ->helperText('برای گزارش‌گیری در Mailtrap.'),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $recipientEmail = $context->recipient['email'] ?? null;
        if (! $recipientEmail && isset($context->recipient['notifiable'])) {
            $recipientEmail = $context->recipient['notifiable']->email ?? null;
        }

        if (! $recipientEmail) {
            return DeliveryResult::failure('missing_email');
        }

        $token = $context->channelSettings['api_token'] ?? config('filament-notify-mailtrap.api_token');
        if (! $token) {
            return DeliveryResult::failure('mailtrap_missing_token');
        }

        $fromAddress = $context->channelSettings['from_address'] ?? config('filament-notify-mailtrap.default_from_address') ?? 'hello@example.com';
        $fromName = $context->channelSettings['from_name'] ?? config('filament-notify-mailtrap.default_from_name') ?? 'Mailtrap';
        $category = $context->channelSettings['category'] ?? config('filament-notify-mailtrap.default_category', 'Filament Notify');

        $body = $message->body;
        if (($message->meta['markdown'] ?? false) === true) {
            $body = Str::markdown($body);
        }

        $payload = [
            'from' => [
                'email' => $fromAddress,
                'name' => $fromName,
            ],
            'to' => [
                [
                    'email' => $recipientEmail,
                ],
            ],
            'subject' => $message->subject ?? 'Notification',
            'text' => strip_tags($body),
            'html' => $body,
            'category' => $category,
        ];

        $response = $this->client($token)->post($this->baseUrl().'/send', $payload);

        if ($response->successful() || $response->status() === 202) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('mailtrap_send_error', [
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
            TextInput::make('to_email')
                ->label('ایمیل گیرنده')
                ->email()
                ->required(),
            TextInput::make('subject')
                ->label('موضوع')
                ->default('تست ارسال Mailtrap')
                ->required(),
            Textarea::make('body')
                ->label('متن پیام')
                ->rows(5)
                ->default('این یک پیام تست از Mailtrap است.')
                ->required()
                ->columnSpanFull(),
            Toggle::make('markdown')
                ->label('ارسال به صورت Markdown')
                ->default(false),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $token = $settings['api_token'] ?? config('filament-notify-mailtrap.api_token');
        if (! $token) {
            return DeliveryResult::failure('توکن ارسال تنظیم نشده است.');
        }

        return DeliveryResult::success([
            'status' => 'token-present',
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $panelId = (string) data_get($context, 'panel.id', 'admin');

        $deliveryContext = ChannelTestContextFactory::make(
            $panelId,
            $this->key(),
            $settings,
            [
                'email' => $data['to_email'] ?? null,
                'notifiable' => $context['user'] ?? null,
            ],
            $context,
        );

        $message = new RenderedMessage(
            subject: (string) ($data['subject'] ?? 'تست ارسال Mailtrap'),
            body: (string) ($data['body'] ?? ''),
            meta: [
                'markdown' => (bool) ($data['markdown'] ?? false),
            ],
        );

        return $this->send($deliveryContext, $message);
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('filament-notify-mailtrap.base_url', 'https://send.api.mailtrap.io/api'), '/');
    }

    protected function client(string $token)
    {
        $headers = [];
        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;
        if (is_string($correlationId) && $correlationId !== '') {
            $headers['X-Correlation-Id'] = $correlationId;
        }

        return Http::withHeaders($headers)
            ->withToken($token)
            ->acceptJson();
    }
}
