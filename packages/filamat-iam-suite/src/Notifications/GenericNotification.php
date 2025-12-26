<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $type, public readonly array $payload = []) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('پیام سیستم')
            ->line('نوع پیام: '.$this->type)
            ->line('اطلاعات: '.json_encode($this->payload, JSON_UNESCAPED_UNICODE));
    }
}
