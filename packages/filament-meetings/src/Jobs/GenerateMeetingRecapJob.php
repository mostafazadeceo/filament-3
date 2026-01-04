<?php

namespace Haida\FilamentMeetings\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingsAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMeetingRecapJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $meetingId,
        public int $tenantId,
        public ?int $actorId = null,
    ) {}

    public function handle(MeetingsAiService $service): void
    {
        $tenant = Tenant::query()->find($this->tenantId);
        if (! $tenant) {
            return;
        }

        TenantContext::setTenant($tenant);

        try {
            $meeting = Meeting::query()->find($this->meetingId);
            if (! $meeting) {
                return;
            }

            $actor = $this->resolveActor();
            $service->generateRecap($meeting, $actor);
        } finally {
            TenantContext::setTenant(null);
        }
    }

    protected function resolveActor(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        if (! $this->actorId) {
            return null;
        }

        $userModel = config('auth.providers.users.model');

        return $userModel::query()->find($this->actorId);
    }
}
