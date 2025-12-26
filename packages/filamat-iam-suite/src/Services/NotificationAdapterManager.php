<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Services\Adapters\CustomNotificationAdapter;
use Filamat\IamSuite\Services\Adapters\LaravelNotificationAdapter;
use Filamat\IamSuite\Services\Adapters\NullNotificationAdapter;
use InvalidArgumentException;

class NotificationAdapterManager
{
    public function driver(): NotificationAdapter
    {
        $driver = (string) config('filamat-iam.notification_adapter', 'custom_plugin');

        return match ($driver) {
            'custom_plugin' => app(CustomNotificationAdapter::class),
            'laravel_notifications' => app(LaravelNotificationAdapter::class),
            'null' => app(NullNotificationAdapter::class),
            default => throw new InvalidArgumentException('Notification adapter is not supported.'),
        };
    }
}
