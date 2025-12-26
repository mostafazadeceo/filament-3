<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Adapters;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Notifications\GenericNotification;
use Filamat\IamSuite\Notifications\OtpNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Notification;

class LaravelNotificationAdapter implements NotificationAdapter
{
    public function sendOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): void
    {
        Notification::send($user, new OtpNotification($purpose, $code, $meta));
    }

    public function verifyOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): bool
    {
        return true;
    }

    public function sendNotification(mixed $target, string $type, array $payload = []): void
    {
        Notification::send($target, new GenericNotification($type, $payload));
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        // Laravel notifications do not require webhooks by default.
    }
}
