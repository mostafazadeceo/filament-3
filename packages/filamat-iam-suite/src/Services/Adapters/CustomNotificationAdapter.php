<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Adapters;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Events\NotificationRequested;
use Filamat\IamSuite\Events\OtpRequested;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomNotificationAdapter implements NotificationAdapter
{
    public function sendOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): void
    {
        event(new OtpRequested($user, $purpose, $code, $meta));
    }

    public function verifyOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): bool
    {
        event('filamat.otp.verify', [$user, $purpose, $code, $meta]);

        return true;
    }

    public function sendNotification(mixed $target, string $type, array $payload = []): void
    {
        event(new NotificationRequested($target, $type, $payload));
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        event('filamat.notification.webhook', [$payload, $headers]);
    }
}
