<?php

namespace Haida\FilamentWorkhub\Jobs;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAiFieldRunJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $fieldId,
        public int $workItemId,
        public ?int $actorId = null,
    ) {}

    public function handle(WorkhubAiService $service): void
    {
        $tenant = Tenant::query()->find($this->tenantId);
        if (! $tenant) {
            return;
        }

        TenantContext::setTenant($tenant);

        try {
            $field = CustomField::query()->withoutGlobalScopes()->find($this->fieldId);
            $workItem = WorkItem::query()->withoutGlobalScopes()->find($this->workItemId);

            if (! $field || ! $workItem) {
                return;
            }

            $actor = $this->actorId ? User::query()->find($this->actorId) : null;
            $service->generateAiField($field, $workItem, $actor);
        } finally {
            TenantContext::setTenant(null);
        }
    }
}
