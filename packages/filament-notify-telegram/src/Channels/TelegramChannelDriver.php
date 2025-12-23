<?php

namespace Haida\FilamentNotify\Telegram\Channels;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Support\Facades\Http;

class TelegramChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'telegram';
    }

    public function label(): string
    {
        return 'تلگرام';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            TextInput::make('bot_token')
                ->label('توکن بات')
                ->password(),
            TextInput::make('default_chat_id')
                ->label('چت آیدی پیش‌فرض')
                ->helperText('در صورت نبودن چت آیدی در گیرنده استفاده می‌شود.'),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $chatId = $context->recipient['telegram_chat_id'] ?? null;
        if (! $chatId && isset($context->recipient['notifiable'])) {
            $chatId = $context->recipient['notifiable']->telegram_chat_id ?? null;
        }

        $chatId = $chatId ?: ($context->channelSettings['default_chat_id'] ?? config('filament-notify-telegram.default_chat_id'));
        $token = $context->channelSettings['bot_token'] ?? config('filament-notify-telegram.bot_token');

        if (! $chatId || ! $token) {
            return DeliveryResult::failure('missing_telegram_credentials');
        }

        $baseUrl = rtrim(config('filament-notify-telegram.base_url'), '/');
        $response = Http::post($baseUrl . '/bot' . $token . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $message->body,
            'parse_mode' => $message->meta['parse_mode'] ?? 'HTML',
        ]);

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('telegram_error', [
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
            TextInput::make('chat_id')
                ->label('چت آیدی')
                ->helperText('اگر خالی باشد از تنظیمات کانال استفاده می‌شود.'),
            Textarea::make('message')
                ->label('متن پیام')
                ->rows(4)
                ->default('پیام تست تلگرام')
                ->required()
                ->columnSpanFull(),
            Select::make('parse_mode')
                ->label('Parse Mode')
                ->options([
                    'HTML' => 'HTML',
                    'Markdown' => 'Markdown',
                    'MarkdownV2' => 'MarkdownV2',
                ])
                ->default('HTML'),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $token = $settings['bot_token'] ?? config('filament-notify-telegram.bot_token');
        if (! $token) {
            return DeliveryResult::failure('توکن بات تلگرام تنظیم نشده است.');
        }

        $baseUrl = rtrim(config('filament-notify-telegram.base_url'), '/');
        $response = Http::get($baseUrl . '/bot' . $token . '/getMe');

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('telegram_connection_error', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $panelId = (string) data_get($context, 'panel.id', 'admin');
        $chatId = $data['chat_id'] ?? null;

        $deliveryContext = ChannelTestContextFactory::make(
            $panelId,
            $this->key(),
            $settings,
            [
                'telegram_chat_id' => $chatId,
                'notifiable' => $context['user'] ?? null,
            ],
            $context,
        );

        $message = new RenderedMessage(
            subject: null,
            body: (string) ($data['message'] ?? ''),
            meta: [
                'parse_mode' => $data['parse_mode'] ?? 'HTML',
            ],
        );

        return $this->send($deliveryContext, $message);
    }
}
