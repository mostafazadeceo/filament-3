<?php

namespace Haida\FilamentNotify\Bale\Channels;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Contracts\SupportsChatIdDiscovery;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Support\Facades\Http;

class BaleChannelDriver implements ChannelDriver, SupportsChatIdDiscovery
{
    public function key(): string
    {
        return 'bale';
    }

    public function label(): string
    {
        return 'بله';
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
                ->helperText('برای ارسال باید کاربر با بات گفتگو را شروع کرده باشد.'),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $chatId = $context->recipient['bale_chat_id'] ?? null;
        if (! $chatId && isset($context->recipient['notifiable'])) {
            $chatId = $context->recipient['notifiable']->bale_chat_id ?? null;
        }

        $chatId = $chatId ?: ($context->channelSettings['default_chat_id'] ?? config('filament-notify-bale.default_chat_id'));
        $token = $context->channelSettings['bot_token'] ?? config('filament-notify-bale.bot_token');

        if (! $chatId || ! $token) {
            return DeliveryResult::failure('missing_bale_credentials');
        }

        $baseUrl = rtrim(config('filament-notify-bale.base_url'), '/');
        $response = Http::post($baseUrl . '/bot' . $token . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $message->body,
        ]);

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('bale_error', [
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
                ->helperText('اگر خالی باشد از تنظیمات کانال استفاده می‌شود. ابتدا کاربر باید با بات گفتگو را شروع کند.'),
            Textarea::make('message')
                ->label('متن پیام')
                ->rows(4)
                ->default('پیام تست بله')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $token = $settings['bot_token'] ?? config('filament-notify-bale.bot_token');
        if (! $token) {
            return DeliveryResult::failure('توکن بات بله تنظیم نشده است.');
        }

        $baseUrl = rtrim(config('filament-notify-bale.base_url'), '/');
        $response = Http::get($baseUrl . '/bot' . $token . '/getMe');

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('bale_connection_error', [
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
                'bale_chat_id' => $chatId,
                'notifiable' => $context['user'] ?? null,
            ],
            $context,
        );

        $message = new RenderedMessage(
            subject: null,
            body: (string) ($data['message'] ?? ''),
            meta: [],
        );

        return $this->send($deliveryContext, $message);
    }

    public function discoverChatIds(array $settings): DeliveryResult
    {
        $token = $settings['bot_token'] ?? config('filament-notify-bale.bot_token');
        if (! $token) {
            return DeliveryResult::failure('missing_bale_credentials');
        }

        $baseUrl = rtrim(config('filament-notify-bale.base_url'), '/');
        $response = Http::get($baseUrl . '/bot' . $token . '/getUpdates');

        if (! $response->successful()) {
            return DeliveryResult::failure('bale_chat_id_discovery_failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
        }

        $updates = $response->json('result') ?? [];
        $chats = [];

        foreach ($updates as $update) {
            $message = $update['message'] ?? $update['channel_post'] ?? null;
            if (! is_array($message)) {
                continue;
            }

            $chat = $message['chat'] ?? null;
            if (! is_array($chat) || ! array_key_exists('id', $chat)) {
                continue;
            }

            $chatId = $chat['id'];
            $key = (string) $chatId;
            unset($chats[$key]);
            $chats[$key] = [
                'id' => $chatId,
                'type' => $chat['type'] ?? null,
                'username' => $chat['username'] ?? null,
                'first_name' => $chat['first_name'] ?? null,
                'last_name' => $chat['last_name'] ?? null,
            ];
        }

        $chats = array_values($chats);
        if (empty($chats)) {
            return DeliveryResult::failure('bale_no_updates');
        }

        return DeliveryResult::success(['chats' => $chats]);
    }
}
