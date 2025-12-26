<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $purpose,
        public readonly string $code,
        public readonly array $meta = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('کد یکبارمصرف')
            ->line('کد شما: '.$this->code)
            ->line('کاربرد: '.$this->purpose);
    }
}
