<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Jobs\SendNotificationJob;
use Filamat\IamSuite\Jobs\SendOtpJob;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Models\OtpCode;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificationService
{
    public function __construct(protected SecurityEventService $securityEventService) {}

    public function sendNotification(mixed $target, string $type, array $payload = [], ?Tenant $tenant = null): Notification
    {
        $tenant ??= TenantContext::getTenant();

        $notification = Notification::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $target instanceof Authenticatable ? $target->getAuthIdentifier() : null,
            'type' => $type,
            'payload' => $payload,
            'status' => 'queued',
        ]);

        SendNotificationJob::dispatch($notification->getKey());

        return $notification;
    }

    public function queueOtp(OtpCode $otpCode, string $plainCode): void
    {
        SendOtpJob::dispatch($otpCode->getKey(), $plainCode);
    }

    public function notifyLogin(Authenticatable $user): void
    {
        $this->sendNotification($user, 'auth.login', ['message' => 'ورود موفق ثبت شد.']);
        $this->securityEventService->record('auth.login', 'info', $user);
    }

    public function notifyLogout(Authenticatable $user): void
    {
        $this->sendNotification($user, 'auth.logout', ['message' => 'خروج ثبت شد.']);
        $this->securityEventService->record('auth.logout', 'info', $user);
    }
}
