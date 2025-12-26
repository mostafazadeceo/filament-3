<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface NotificationAdapter
{
    public function sendOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): void;

    public function verifyOtp(Authenticatable $user, string $purpose, string $code, array $meta = []): bool;

    public function sendNotification(mixed $target, string $type, array $payload = []): void;

    public function handleWebhook(array $payload, array $headers = []): void;
}
