<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Adapters;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Illuminate\Contracts\Auth\Authenticatable;

class NullNotificationAdapter implements NotificationAdapter
{
    public function sendOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): void
    {
        // Intentionally empty.
    }

    public function verifyOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): bool
    {
        return true;
    }

    public function sendNotification(mixed $target, string $type, array $payload = []): void
    {
        // Intentionally empty.
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        // Intentionally empty.
    }
}
