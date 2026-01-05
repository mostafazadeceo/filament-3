<?php

namespace Haida\FilamentNotify\Core\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Contracts\SupportsChatIdDiscovery;
use Haida\FilamentNotify\Core\Models\ChannelSetting;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Illuminate\Support\Str;

class ChannelSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'تنظیمات کانال‌ها';

    protected static ?string $title = 'تنظیمات کانال‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament-notify-core::pages.channel-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getInitialState());
    }

    public function form(Schema $schema): Schema
    {
        $components = [];
        $channels = app(ChannelRegistry::class)->installed();

        foreach ($channels as $driver) {
            $headerActions = [
                $this->makeConnectionTestAction($driver),
                $this->makeSendTestAction($driver),
            ];

            if ($driver instanceof SupportsChatIdDiscovery) {
                $headerActions[] = $this->makeChatIdDiscoveryAction($driver);
            }

            $components[] = Section::make($driver->label())
                ->schema($driver->configSchema())
                ->statePath("channels.{$driver->key()}")
                ->headerActions($headerActions);
        }

        return $schema
            ->components($components)
            ->statePath('data');
    }

    public function save(): void
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        if (! $panelId) {
            return;
        }

        $state = $this->form->getState();
        $channels = $state['channels'] ?? [];

        foreach (app(ChannelRegistry::class)->installed() as $driver) {
            $key = $driver->key();
            $settings = $channels[$key] ?? [];

            ChannelSetting::updateOrCreate(
                [
                    'panel_id' => $panelId,
                    'channel' => $key,
                ],
                [
                    'settings' => $settings,
                ],
            );
        }

        Notification::make()
            ->title('تنظیمات ذخیره شد.')
            ->success()
            ->send();
    }

    protected function getInitialState(): array
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        if (! $panelId) {
            return [];
        }

        $state = [];
        $channels = app(ChannelRegistry::class)->installed();

        foreach ($channels as $driver) {
            $settings = ChannelSetting::query()
                ->where('panel_id', $panelId)
                ->where('channel', $driver->key())
                ->first();

            $state['channels'][$driver->key()] = $settings?->settings ?? [];
        }

        return $state;
    }

    protected function makeConnectionTestAction(ChannelDriver $driver): Action
    {
        $key = $driver->key();
        $formSchema = $driver->connectionTestForm();

        $action = Action::make('test_connection_'.$key)
            ->label('تست اتصال')
            ->icon('heroicon-o-link')
            ->color('gray')
            ->extraAttributes(['data-fn-ignore' => true])
            ->action(function (array $data = []) use ($driver, $key): void {
                $settings = $this->getChannelSettingsFromForm($key);
                $result = $driver->testConnection($settings, $data);
                $this->notifyTestResult($driver->key(), $driver->label(), 'اتصال', $result);
            });

        if (! empty($formSchema)) {
            $action->form($formSchema)
                ->modalHeading('تست اتصال - '.$driver->label())
                ->modalSubmitActionLabel('بررسی');
        }

        return $action;
    }

    protected function makeSendTestAction(ChannelDriver $driver): Action
    {
        $key = $driver->key();

        return Action::make('test_send_'.$key)
            ->label('تست ارسال')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->extraAttributes(['data-fn-ignore' => true])
            ->form($driver->sendTestForm())
            ->modalHeading('تست ارسال - '.$driver->label())
            ->modalSubmitActionLabel('ارسال')
            ->action(function (array $data) use ($driver, $key): void {
                $settings = $this->getChannelSettingsFromForm($key);
                $context = $this->buildTestContext($data);
                $result = $driver->testSend($settings, $data, $context);
                $this->notifyTestResult($driver->key(), $driver->label(), 'ارسال', $result);
            });
    }

    protected function makeChatIdDiscoveryAction(SupportsChatIdDiscovery $driver): Action
    {
        $key = $driver->key();

        return Action::make('discover_chat_id_'.$key)
            ->label('دریافت چت آیدی')
            ->icon('heroicon-o-identification')
            ->color('gray')
            ->extraAttributes(['data-fn-ignore' => true])
            ->action(function () use ($driver, $key): void {
                $settings = $this->getChannelSettingsFromForm($key);
                $result = $driver->discoverChatIds($settings);

                if (! $result->success) {
                    $this->notifyTestResult($driver->key(), $driver->label(), 'دریافت چت آیدی', $result);

                    return;
                }

                $chats = $result->response['chats'] ?? [];
                $chat = null;
                if (is_array($chats) && $chats !== []) {
                    $chat = $chats[array_key_last($chats)] ?? null;
                }
                if (! is_array($chat) || ! array_key_exists('id', $chat)) {
                    $this->notifyTestResult(
                        $driver->key(),
                        $driver->label(),
                        'دریافت چت آیدی',
                        DeliveryResult::failure('bale_no_updates'),
                    );

                    return;
                }

                $state = $this->form->getState();
                data_set($state, "channels.{$key}.default_chat_id", $chat['id']);
                $this->form->fill($state);

                $this->persistChannelSetting($key, data_get($state, "channels.{$key}", []));

                $label = $this->formatChatLabel($chat);
                Notification::make()
                    ->title('چت آیدی تنظیم شد.')
                    ->body($label ?: ('چت آیدی: '.$chat['id']))
                    ->success()
                    ->send();
            });
    }

    protected function getChannelSettingsFromForm(string $key): array
    {
        $state = $this->form->getState();

        return $state['channels'][$key] ?? [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function buildTestContext(array $data): array
    {
        $panel = Filament::getCurrentPanel();

        return [
            'record' => null,
            'record_id' => null,
            'record_type' => null,
            'user' => Filament::auth()?->user(),
            'tenant' => Filament::getTenant(),
            'panel' => [
                'id' => $panel?->getId(),
                'path' => $panel?->getPath(),
            ],
            'action' => [
                'name' => 'test',
                'label' => 'تست ارسال',
                'data' => $data,
                'context' => [],
                'livewire' => static::class,
            ],
            'test' => $data,
        ];
    }

    protected function notifyTestResult(string $channelKey, string $channelLabel, string $type, DeliveryResult $result): void
    {
        $title = $result->success
            ? "تست {$type} {$channelLabel} موفق بود"
            : "تست {$type} {$channelLabel} ناموفق بود";

        $notification = Notification::make()->title($title);

        $body = $this->formatResultBody($channelKey, $result);
        if ($body) {
            $notification->body($body);
        }

        $result->success ? $notification->success() : $notification->danger();
        $notification->send();
    }

    protected function formatResultBody(string $channelKey, DeliveryResult $result): ?string
    {
        $lines = [];

        if ($result->error) {
            $lines[] = $this->mapErrorMessage($result->error);
        }

        if (is_array($result->response)) {
            $lines = array_merge($lines, $this->summarizeResponse($channelKey, $result->response));
        } elseif (is_string($result->response)) {
            $lines[] = $result->response;
        }

        $lines = array_values(array_unique(array_filter($lines)));

        if (empty($lines)) {
            return $result->success ? 'عملیات با موفقیت انجام شد.' : null;
        }

        $lines = array_map(static fn (string $line): string => Str::limit($line, 200), $lines);

        return Str::limit(implode(' | ', $lines), 400);
    }

    protected function mapErrorMessage(string $error): string
    {
        return match ($error) {
            'channel_not_available' => 'کانال نصب یا فعال نیست.',
            'missing_template' => 'قالب برای این کانال انتخاب نشده است.',
            'missing_email' => 'ایمیل گیرنده پیدا نشد.',
            'missing_phone' => 'شماره گیرنده پیدا نشد.',
            'smtp_not_configured' => 'SMTP فعال نیست. ابتدا تنظیمات SMTP را وارد کنید.',
            'smtp_connection_error' => 'اتصال به SMTP ناموفق بود.',
            'missing_pattern_code' => 'کد پترن وارد نشده است.',
            'missing_sms_body' => 'متن پیامک وارد نشده است.',
            'missing_from_number' => 'شماره فرستنده تنظیم نشده است.',
            'missing_api_credentials' => 'توکن یا آدرس API ناقص است.',
            'ippanel_connection_error' => 'اتصال به IPPanel برقرار نشد.',
            'ippanel_error' => 'ارسال پیامک IPPanel ناموفق بود.',
            'missing_telegram_credentials' => 'توکن بات یا چت آیدی تلگرام ناقص است.',
            'telegram_connection_error' => 'اتصال به تلگرام برقرار نشد.',
            'telegram_error' => 'ارسال پیام تلگرام ناموفق بود.',
            'missing_bale_credentials' => 'توکن بات یا چت آیدی بله ناقص است.',
            'bale_connection_error' => 'اتصال به بله برقرار نشد.',
            'bale_error' => 'ارسال پیام بله ناموفق بود.',
            'bale_chat_id_discovery_failed' => 'خواندن چت آیدی بله ناموفق بود.',
            'bale_no_updates' => 'هیچ پیامی از بله دریافت نشد. ابتدا در بله با بات /start بزنید.',
            'missing_whatsapp_credentials' => 'توکن یا Phone Number ID واتساپ ناقص است.',
            'missing_template_name' => 'نام تمپلیت واتساپ وارد نشده است.',
            'whatsapp_connection_error' => 'اتصال به واتساپ برقرار نشد.',
            'whatsapp_error' => 'ارسال پیام واتساپ ناموفق بود.',
            'missing_vapid_keys' => 'کلیدهای VAPID تنظیم نشده است.',
            'no_subscriptions' => 'هیچ اشتراک وب‌پوشی برای این کاربر وجود ندارد.',
            'webpush_error' => 'ارسال وب‌پوش ناموفق بود.',
            'missing_notifiable' => 'کاربر گیرنده مشخص نشده است.',
            default => $error,
        };
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function persistChannelSetting(string $key, array $settings): void
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        if (! $panelId) {
            return;
        }

        ChannelSetting::updateOrCreate(
            [
                'panel_id' => $panelId,
                'channel' => $key,
            ],
            [
                'settings' => $settings,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $chat
     */
    protected function formatChatLabel(array $chat): string
    {
        $parts = [];

        if (! empty($chat['username'])) {
            $parts[] = '@'.$chat['username'];
        }

        $name = trim((string) ($chat['first_name'] ?? '').' '.(string) ($chat['last_name'] ?? ''));
        if ($name !== '') {
            $parts[] = $name;
        }

        if (! empty($chat['id'])) {
            $parts[] = 'ID: '.$chat['id'];
        }

        return implode(' | ', $parts);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeResponse(string $channelKey, array $response): array
    {
        return match ($channelKey) {
            'sms_ippanel' => $this->summarizeIppanelResponse($response),
            'telegram' => $this->summarizeTelegramResponse($response),
            'bale' => $this->summarizeBaleResponse($response),
            'whatsapp' => $this->summarizeWhatsAppResponse($response),
            'webpush' => $this->summarizeWebPushResponse($response),
            'mail' => $this->summarizeMailResponse($response),
            default => $this->summarizeGenericResponse($response),
        };
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeIppanelResponse(array $response): array
    {
        $lines = [];

        if (array_key_exists('status', $response)) {
            $status = $response['status'];
            $lines[] = 'وضعیت: '.($status === true ? 'موفق' : ($status === false ? 'ناموفق' : (string) $status));
        }

        if (! empty($response['message'])) {
            $lines[] = 'پیام: '.$response['message'];
        }

        if (! empty($response['message_code'])) {
            $lines[] = 'کد پیام: '.$response['message_code'];
        }

        $data = $response['data'] ?? null;
        if (is_array($data)) {
            if (! empty($data['user_name'])) {
                $lines[] = 'کاربر: '.$data['user_name'];
            } elseif (! empty($data['name'])) {
                $lines[] = 'کاربر: '.$data['name'];
            }

            if (! empty($data['user_id'])) {
                $lines[] = 'شناسه کاربر: '.$data['user_id'];
            }

            if (! empty($data['mobile'])) {
                $lines[] = 'موبایل: '.$data['mobile'];
            }
        }

        return $lines ?: $this->summarizeGenericResponse($response);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeTelegramResponse(array $response): array
    {
        $lines = [];

        if (isset($response['status']) && (int) $response['status'] === 404) {
            return ['چت آیدی پیدا نشد یا کاربر با بات گفتگو را شروع نکرده است.'];
        }

        if (array_key_exists('ok', $response)) {
            $lines[] = 'وضعیت: '.($response['ok'] ? 'موفق' : 'ناموفق');
        }

        $result = $response['result'] ?? null;
        if (is_array($result)) {
            if (! empty($result['message_id'])) {
                $lines[] = 'شناسه پیام: '.$result['message_id'];
            }

            $username = $result['username'] ?? null;
            $firstName = $result['first_name'] ?? null;
            if ($username || $firstName) {
                $label = $firstName ?: $username;
                $suffix = $username ? ' (@'.$username.')' : '';
                $lines[] = 'بات: '.$label.$suffix;
            }
        }

        return $lines ?: $this->summarizeGenericResponse($response);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeBaleResponse(array $response): array
    {
        if (isset($response['status']) && (int) $response['status'] === 404) {
            return ['چت آیدی پیدا نشد یا کاربر با بات گفتگو را شروع نکرده است.'];
        }

        return $this->summarizeTelegramResponse($response);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeWhatsAppResponse(array $response): array
    {
        $lines = [];

        if (! empty($response['messages'][0]['id'])) {
            $lines[] = 'شناسه پیام: '.$response['messages'][0]['id'];
        }

        if (! empty($response['contacts'][0]['wa_id'])) {
            $lines[] = 'شماره واتساپ: '.$response['contacts'][0]['wa_id'];
        }

        return $lines ?: $this->summarizeGenericResponse($response);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeWebPushResponse(array $response): array
    {
        $errors = $response['errors'] ?? null;
        if (is_array($errors) && ! empty($errors)) {
            return ['خطاها: '.count($errors).' مورد'];
        }

        return ['اعلان ارسال شد.'];
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeMailResponse(array $response): array
    {
        $lines = [];

        if (! empty($response['mailer'])) {
            $lines[] = 'سرویس: '.$response['mailer'];
        }

        if (! empty($response['transport'])) {
            $lines[] = 'ترنسپورت: '.$response['transport'];
        }

        if (! empty($response['host'])) {
            $lines[] = 'هاست: '.$response['host'];
        }

        if (! empty($response['port'])) {
            $lines[] = 'پورت: '.$response['port'];
        }

        if (! empty($response['scheme'])) {
            $lines[] = 'نوع اتصال: '.$response['scheme'];
        }

        if (! empty($response['message'])) {
            $lines[] = 'پیام: '.$response['message'];
        }

        return $lines ?: ['ایمیل ارسال شد.'];
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, string>
     */
    protected function summarizeGenericResponse(array $response): array
    {
        $lines = [];

        if (array_key_exists('status', $response)) {
            $status = $response['status'];
            $lines[] = 'وضعیت: '.($status === true ? 'موفق' : ($status === false ? 'ناموفق' : (string) $status));
        }

        if (! empty($response['message'])) {
            $lines[] = 'پیام: '.$response['message'];
        }

        if (! empty($response['message_code'])) {
            $lines[] = 'کد پیام: '.$response['message_code'];
        }

        if (! empty($response['code'])) {
            $lines[] = 'کد: '.$response['code'];
        }

        if (! empty($response['status']) && isset($response['body'])) {
            $lines[] = 'کد HTTP: '.$response['status'];
            if (is_string($response['body']) && $response['body'] !== '') {
                $lines[] = 'پاسخ: '.$response['body'];
            }
        }

        return $lines ?: ['پاسخ دریافت شد.'];
    }
}
