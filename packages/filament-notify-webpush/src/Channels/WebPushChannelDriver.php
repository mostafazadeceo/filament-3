<?php

namespace Haida\FilamentNotify\WebPush\Channels;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Haida\FilamentNotify\WebPush\Models\WebPushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'webpush';
    }

    public function label(): string
    {
        return 'وب‌پوش';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            Fieldset::make('VAPID')
                ->schema([
                    TextInput::make('vapid_public_key')
                        ->label('کلید عمومی VAPID')
                        ->helperText('اگر خالی باشد از env استفاده می‌شود.'),
                    TextInput::make('vapid_private_key')
                        ->label('کلید خصوصی VAPID')
                        ->password()
                        ->revealable()
                        ->helperText('اگر خالی باشد از env استفاده می‌شود.'),
                    TextInput::make('vapid_subject')
                        ->label('VAPID Subject')
                        ->helperText('مثال: mailto:admin@example.com یا https://example.com'),
                ])
                ->columns(2),
            Fieldset::make('اجازه وب‌پوش')
                ->schema([
                    Toggle::make('prompt_enabled')
                        ->label('نمایش درخواست اجازه')
                        ->default(true),
                    Toggle::make('auto_subscribe')
                        ->label('ثبت خودکار اشتراک بعد از تایید')
                        ->default(true),
                    Select::make('prompt_position')
                        ->label('موقعیت پاپ‌آپ')
                        ->options([
                            'bottom-left' => 'پایین چپ',
                            'bottom-right' => 'پایین راست',
                            'top-left' => 'بالا چپ',
                            'top-right' => 'بالا راست',
                        ])
                        ->default('bottom-left'),
                    TextInput::make('prompt_repeat_minutes')
                        ->label('دوره نمایش مجدد (دقیقه)')
                        ->numeric()
                        ->helperText('۰ یعنی همیشه نمایش داده شود.')
                        ->default(1440),
                    TextInput::make('prompt_delay_seconds')
                        ->label('تاخیر نمایش (ثانیه)')
                        ->numeric()
                        ->default(2),
                    TextInput::make('prompt_auto_dismiss_seconds')
                        ->label('بستن خودکار پاپ‌آپ (ثانیه)')
                        ->numeric()
                        ->helperText('۰ یعنی پاپ‌آپ تا زمانی که کاربر تصمیم بگیرد نمایش داده شود.')
                        ->default(15),
                    TextInput::make('prompt_title')
                        ->label('عنوان پاپ‌آپ'),
                    Textarea::make('prompt_body')
                        ->label('متن پاپ‌آپ')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('prompt_allow_label')
                        ->label('متن دکمه فعال‌سازی')
                        ->default('فعال‌سازی'),
                    TextInput::make('prompt_dismiss_label')
                        ->label('متن دکمه بعداً')
                        ->default('بعداً'),
                    TextInput::make('prompt_link_label')
                        ->label('متن لینک')
                        ->helperText('اختیاری'),
                    TextInput::make('prompt_link_url')
                        ->label('آدرس لینک')
                        ->helperText('اختیاری - https://...'),
                    TextInput::make('prompt_image_url')
                        ->label('تصویر بنر (URL)')
                        ->helperText('اختیاری - https://...'),
                    TextInput::make('prompt_image_alt')
                        ->label('متن جایگزین تصویر')
                        ->helperText('اختیاری'),
                ])
                ->columns(2),
            Fieldset::make('پیام خوش‌آمد')
                ->schema([
                    Toggle::make('welcome_enabled')
                        ->label('ارسال پیام خوش‌آمد بعد از فعال‌سازی')
                        ->default(false),
                    TextInput::make('welcome_title')
                        ->label('عنوان پیام خوش‌آمد')
                        ->default('وب‌پوش فعال شد'),
                    Textarea::make('welcome_body')
                        ->label('متن پیام خوش‌آمد')
                        ->rows(3)
                        ->default('از این پس اعلان‌های مهم را دریافت می‌کنید.')
                        ->columnSpanFull(),
                    TextInput::make('welcome_url')
                        ->label('لینک پیام خوش‌آمد')
                        ->helperText('اختیاری - https://...'),
                    TextInput::make('welcome_icon')
                        ->label('آیکون پیام خوش‌آمد')
                        ->helperText('اختیاری - https://...'),
                ])
                ->columns(2),
        ];
    }

    public function supportsTemplates(): bool
    {
        return true;
    }

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult
    {
        $notifiable = $context->recipient['notifiable'] ?? null;
        if (! $notifiable || ! method_exists($notifiable, 'getKey')) {
            return DeliveryResult::failure('missing_notifiable');
        }

        $subscriptions = WebPushSubscription::query()
            ->where('user_id', $notifiable->getKey())
            ->get();

        if ($subscriptions->isEmpty()) {
            return DeliveryResult::failure('no_subscriptions');
        }

        $publicKey = $context->channelSettings['vapid_public_key']
            ?? config('webpush.vapid.public_key')
            ?? env('VAPID_PUBLIC_KEY');
        $privateKey = $context->channelSettings['vapid_private_key']
            ?? config('webpush.vapid.private_key')
            ?? env('VAPID_PRIVATE_KEY');
        $subject = $context->channelSettings['vapid_subject']
            ?? config('webpush.vapid.subject')
            ?? env('VAPID_SUBJECT')
            ?? config('app.url');

        if (! $publicKey || ! $privateKey) {
            return DeliveryResult::failure('missing_vapid_keys');
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);

        $payload = [
            'title' => $message->meta['title'] ?? $message->subject ?? 'اعلان جدید',
            'body' => $message->body,
            'icon' => $message->meta['icon'] ?? null,
            'url' => $message->meta['url'] ?? null,
        ];

        $success = false;
        $errors = [];

        foreach ($subscriptions as $subscription) {
            $sub = Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => $subscription->content_encoding ?? 'aesgcm',
            ]);

            $report = $webPush->sendOneNotification($sub, json_encode($payload, JSON_UNESCAPED_UNICODE));
            if ($report->isSuccess()) {
                $success = true;
            } else {
                $errors[] = $report->getReason();
            }
        }

        if ($success) {
            return DeliveryResult::success(['errors' => $errors]);
        }

        return DeliveryResult::failure('webpush_error', ['errors' => $errors]);
    }

    public function connectionTestForm(): array
    {
        return [];
    }

    public function sendTestForm(): array
    {
        return [
            TextInput::make('title')
                ->label('عنوان')
                ->default('اعلان تست'),
            Textarea::make('body')
                ->label('متن اعلان')
                ->rows(4)
                ->default('این یک اعلان تست است.')
                ->columnSpanFull(),
            TextInput::make('url')
                ->label('لینک کلیک')
                ->placeholder('https://example.com'),
            TextInput::make('icon')
                ->label('آیکون (URL)')
                ->placeholder('https://example.com/icon.png'),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        $publicKey = $settings['vapid_public_key'] ?? config('webpush.vapid.public_key') ?? env('VAPID_PUBLIC_KEY');
        $privateKey = $settings['vapid_private_key'] ?? config('webpush.vapid.private_key') ?? env('VAPID_PRIVATE_KEY');
        $subject = $settings['vapid_subject'] ?? config('webpush.vapid.subject') ?? env('VAPID_SUBJECT');

        if (! $publicKey || ! $privateKey) {
            return DeliveryResult::failure('کلیدهای VAPID کامل نیستند.');
        }

        return DeliveryResult::success([
            'vapid_public_key' => $publicKey,
            'vapid_subject' => $subject,
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $user = $context['user'] ?? null;
        if (! $user) {
            return DeliveryResult::failure('کاربر وارد نشده است.');
        }

        $panelId = (string) data_get($context, 'panel.id', 'admin');
        $deliveryContext = ChannelTestContextFactory::make(
            $panelId,
            $this->key(),
            $settings,
            ['notifiable' => $user],
            $context,
        );

        $message = new RenderedMessage(
            subject: null,
            body: (string) ($data['body'] ?? ''),
            meta: [
                'title' => $data['title'] ?? 'اعلان تست',
                'url' => $data['url'] ?? null,
                'icon' => $data['icon'] ?? null,
            ],
        );

        return $this->send($deliveryContext, $message);
    }
}
