<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Jobs;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public int $notificationId) {}

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(NotificationAdapter $adapter): void
    {
        $notification = Notification::query()->find($this->notificationId);
        if (! $notification) {
            return;
        }

        if ($notification->tenant_id) {
            TenantContext::setTenant($notification->tenant);
        }

        $target = $notification->user;
        if (! $target) {
            $notification->update(['status' => 'skipped']);

            return;
        }

        try {
            $adapter->sendNotification($target, $notification->type, $notification->payload ?? []);
            $notification->update(['status' => 'sent']);
        } catch (\Throwable $exception) {
            $notification->update(['status' => 'failed']);
            throw $exception;
        }
    }
}
