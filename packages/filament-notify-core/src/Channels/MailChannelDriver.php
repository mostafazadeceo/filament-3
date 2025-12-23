<?php

namespace Haida\FilamentNotify\Core\Channels;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Haida\FilamentNotify\Core\Contracts\ChannelDriver;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Mail\GenericNotificationMail;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;

class MailChannelDriver implements ChannelDriver
{
    public function key(): string
    {
        return 'mail';
    }

    public function label(): string
    {
        return 'ایمیل';
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function configSchema(): array
    {
        return [
            TextInput::make('from_address')
                ->label('آدرس فرستنده')
                ->email()
                ->helperText('اگر خالی باشد از تنظیمات اصلی لاراول استفاده می‌شود.'),
            TextInput::make('from_name')
                ->label('نام فرستنده')
                ->helperText('اختیاری.'),
            TextInput::make('smtp_host')
                ->label('SMTP Host')
                ->helperText('برای جیمیل: smtp.gmail.com'),
            TextInput::make('smtp_port')
                ->label('SMTP Port')
                ->numeric()
                ->helperText('587 برای TLS یا 465 برای SSL'),
            Select::make('smtp_scheme')
                ->label('نوع اتصال')
                ->options([
                    'smtp' => 'SMTP (STARTTLS)',
                    'smtps' => 'SMTPS (SSL)',
                ])
                ->helperText('برای جیمیل: SMTP + 587 یا SMTPS + 465'),
            TextInput::make('smtp_username')
                ->label('نام کاربری SMTP')
                ->helperText('معمولاً ایمیل کامل است.'),
            TextInput::make('smtp_password')
                ->label('رمز عبور SMTP')
                ->password()
                ->revealable()
                ->helperText('برای جیمیل از App Password استفاده کنید.'),
            TextInput::make('smtp_timeout')
                ->label('مهلت اتصال (ثانیه)')
                ->numeric()
                ->helperText('اختیاری.'),
            TextInput::make('smtp_local_domain')
                ->label('Local Domain (EHLO)')
                ->helperText('اختیاری. در صورت نیاز برای HELO/EHLO استفاده می‌شود.'),
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

        $fromAddress = $context->channelSettings['from_address'] ?? config('filament-notify.mail.from_address');
        $fromName = $context->channelSettings['from_name'] ?? config('filament-notify.mail.from_name');

        $body = $message->body;
        if (($message->meta['markdown'] ?? false) === true) {
            $body = Str::markdown($body);
        }

        $mailerName = $this->resolveMailerName($context->channelSettings ?? []);

        Mail::mailer($mailerName)->to($recipientEmail)->send(new GenericNotificationMail(
            subjectLine: $message->subject,
            htmlBody: $body,
            fromAddress: $fromAddress,
            fromName: $fromName,
        ));

        return DeliveryResult::success();
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
                ->default('تست ارسال ایمیل')
                ->required(),
            Textarea::make('body')
                ->label('متن پیام')
                ->rows(5)
                ->default('این یک پیام تست است.')
                ->required()
                ->columnSpanFull(),
            Toggle::make('markdown')
                ->label('ارسال به صورت Markdown')
                ->default(false),
        ];
    }

    public function testConnection(array $settings, array $data = []): DeliveryResult
    {
        [$mailerName, $config] = $this->resolveMailerConfig($settings);

        if (! is_array($config) || ($config['transport'] ?? null) !== 'smtp') {
            return DeliveryResult::failure('smtp_not_configured');
        }

        if (empty($config['host'])) {
            return DeliveryResult::failure('smtp_not_configured');
        }

        try {
            $mailer = Mail::mailer($mailerName);
            $transport = $mailer->getSymfonyTransport();

            if (! $transport instanceof SmtpTransport) {
                return DeliveryResult::failure('smtp_not_configured');
            }

            $transport->start();
            $transport->stop();
        } catch (\Throwable $exception) {
            return DeliveryResult::failure('smtp_connection_error', [
                'message' => $exception->getMessage(),
                'host' => $config['host'] ?? null,
                'port' => $config['port'] ?? null,
                'scheme' => $config['scheme'] ?? null,
            ]);
        }

        return DeliveryResult::success([
            'mailer' => $mailerName,
            'transport' => 'smtp',
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'scheme' => $config['scheme'] ?? null,
        ]);
    }

    public function testSend(array $settings, array $data, array $context = []): DeliveryResult
    {
        $recipientEmail = $data['to_email'] ?? null;
        if (! $recipientEmail) {
            return DeliveryResult::failure('ایمیل گیرنده وارد نشده است.');
        }

        $subject = (string) ($data['subject'] ?? 'تست ارسال ایمیل');
        $body = (string) ($data['body'] ?? '');
        $markdown = (bool) ($data['markdown'] ?? false);

        $fromAddress = $settings['from_address'] ?? config('filament-notify.mail.from_address');
        $fromName = $settings['from_name'] ?? config('filament-notify.mail.from_name');

        if ($markdown) {
            $body = Str::markdown($body);
        }

        try {
            $mailerName = $this->resolveMailerName($settings);

            Mail::mailer($mailerName)->to($recipientEmail)->send(new GenericNotificationMail(
                subjectLine: $subject,
                htmlBody: $body,
                fromAddress: $fromAddress,
                fromName: $fromName,
            ));
        } catch (\Throwable $exception) {
            return DeliveryResult::failure($exception->getMessage());
        }

        return DeliveryResult::success();
    }

    /**
     * @return array{string, array<string, mixed>|null}
     */
    protected function resolveMailerConfig(array $settings): array
    {
        $smtpConfig = $this->buildCustomSmtpConfig($settings);
        if ($smtpConfig) {
            $mailerName = 'filament_notify_smtp';
            config()->set("mail.mailers.{$mailerName}", $smtpConfig);
            Mail::forgetMailers();

            return [$mailerName, $smtpConfig];
        }

        $mailerName = (string) config('mail.default', 'log');
        $config = config("mail.mailers.{$mailerName}");

        return [$mailerName, is_array($config) ? $config : null];
    }

    protected function resolveMailerName(array $settings): string
    {
        [$mailerName] = $this->resolveMailerConfig($settings);

        return $mailerName;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function buildCustomSmtpConfig(array $settings): ?array
    {
        $host = trim((string) ($settings['smtp_host'] ?? ''));
        if ($host === '') {
            return null;
        }

        $scheme = trim((string) ($settings['smtp_scheme'] ?? ''));
        $scheme = $scheme !== '' ? $scheme : null;

        $port = (int) ($settings['smtp_port'] ?? 0);
        if (! $port) {
            $port = $scheme === 'smtps' ? 465 : 587;
        }

        $config = [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'username' => $settings['smtp_username'] ?? null,
            'password' => $settings['smtp_password'] ?? null,
        ];

        if ($scheme !== null) {
            $config['scheme'] = $scheme;
        }

        if (! empty($settings['smtp_timeout'])) {
            $config['timeout'] = (int) $settings['smtp_timeout'];
        }

        if (! empty($settings['smtp_local_domain'])) {
            $config['local_domain'] = (string) $settings['smtp_local_domain'];
        }

        return $config;
    }
}
