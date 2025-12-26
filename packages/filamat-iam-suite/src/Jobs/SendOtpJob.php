<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Jobs;

use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Models\OtpCode;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public int $otpCodeId, public string $plainCode) {}

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(NotificationAdapter $adapter): void
    {
        $otp = OtpCode::query()->find($this->otpCodeId);
        if (! $otp) {
            return;
        }

        if ($otp->tenant_id) {
            TenantContext::setTenant($otp->tenant);
        }

        $user = $otp->user;
        if (! $user) {
            return;
        }

        $adapter->sendOtp($user, $otp->purpose, $this->plainCode, ['otp_id' => $otp->getKey()]);
    }
}
