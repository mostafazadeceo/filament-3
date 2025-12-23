<?php

namespace Haida\FilamentNotify\WhatsApp\Channels;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Rendering\TemplateRenderer;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Illuminate\Support\Facades\Http;

class WhatsAppChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'whatsapp';
    }

    public function label(): string
    {
        return 'واتساپ';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            TextInput::make('token')
                ->label('توکن API')
                ->password(),
            TextInput::make('phone_number_id')
                ->label('Phone Number ID'),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $recipient = $context->recipient['whatsapp_number'] ?? $context->recipient['phone'] ?? null;
        if (! $recipient && isset($context->recipient['notifiable'])) {
            $recipient = $context->recipient['notifiable']->whatsapp_number
                ?? $context->recipient['notifiable']->phone
                ?? $context->recipient['notifiable']->mobile
                ?? null;
        }

        $token = $context->channelSettings['token'] ?? config('filament-notify-whatsapp.token');
        $phoneNumberId = $context->channelSettings['phone_number_id'] ?? config('filament-notify-whatsapp.phone_number_id');

        if (! $recipient || ! $token || ! $phoneNumberId) {
            return DeliveryResult::failure('missing_whatsapp_credentials');
        }

        $templateName = $message->meta['template_name'] ?? null;
        if (! $templateName) {
            return DeliveryResult::failure('missing_template_name');
        }

        $language = $message->meta['language'] ?? 'en_US';
        $components = $message->meta['components'] ?? [];

        if (is_array($components)) {
            $renderer = app(TemplateRenderer::class);
            array_walk_recursive($components, function (&$value) use ($renderer, $context): void {
                if (is_string($value)) {
                    $value = $renderer->renderString($value, $context->context);
                }
            });
        } else {
            $components = [];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $recipient,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
                'components' => $components,
            ],
        ];

        $baseUrl = rtrim(config('filament-notify-whatsapp.base_url'), '/');
        $response = Http::withToken($token)
            ->post($baseUrl . '/' . $phoneNumberId . '/messages', $payload);

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('whatsapp_error', [
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
            TextInput::make('to')
                ->label('شماره گیرنده')
                ->required(),
            TextInput::make('template_name')
                ->label('نام تمپلیت')
                ->required(),
            TextInput::make('language')
                ->label('کد زبان')
                ->default('en_US'),
            Textarea::make('components')
                ->label('کامپوننت‌ها (JSON)')
                ->default('[]')
                ->rows(4)
                ->rules(['json'])
                ->columnSpanFull(),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $token = $settings['token'] ?? config('filament-notify-whatsapp.token');
        $phoneNumberId = $settings['phone_number_id'] ?? config('filament-notify-whatsapp.phone_number_id');

        if (! $token || ! $phoneNumberId) {
            return DeliveryResult::failure('توکن یا Phone Number ID تنظیم نشده است.');
        }

        $baseUrl = rtrim(config('filament-notify-whatsapp.base_url'), '/');
        $response = Http::withToken($token)->get($baseUrl . '/' . $phoneNumberId, [
            'fields' => 'display_phone_number,verified_name',
        ]);

        if ($response->successful()) {
            return DeliveryResult::success($response->json());
        }

        return DeliveryResult::failure('whatsapp_connection_error', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $recipient = $data['to'] ?? null;
        if (! $recipient) {
            return DeliveryResult::failure('شماره گیرنده وارد نشده است.');
        }

        $templateName = $data['template_name'] ?? null;
        if (! $templateName) {
            return DeliveryResult::failure('نام تمپلیت وارد نشده است.');
        }

        $components = $data['components'] ?? [];
        if (is_string($components)) {
            $decoded = json_decode($components, true);
            if (! is_array($decoded)) {
                return DeliveryResult::failure('کامپوننت‌های JSON معتبر نیستند.');
            }
            $components = $decoded;
        }

        if (! is_array($components)) {
            return DeliveryResult::failure('کامپوننت‌های JSON معتبر نیستند.');
        }

        $panelId = (string) data_get($context, 'panel.id', 'admin');
        $deliveryContext = ChannelTestContextFactory::make(
            $panelId,
            $this->key(),
            $settings,
            ['whatsapp_number' => $recipient],
            $context,
        );

        $message = new RenderedMessage(
            subject: null,
            body: '',
            meta: [
                'template_name' => $templateName,
                'language' => $data['language'] ?? 'en_US',
                'components' => $components,
            ],
        );

        return $this->send($deliveryContext, $message);
    }
}
