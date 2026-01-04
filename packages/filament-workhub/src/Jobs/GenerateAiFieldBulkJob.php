<?php

namespace Haida\FilamentWorkhub\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAiFieldBulkJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $fieldId,
        public ?int $actorId = null,
        public int $limit = 100,
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::query()->find($this->tenantId);
        if (! $tenant) {
            return;
        }

        TenantContext::setTenant($tenant);

        try {
            $field = CustomField::query()->withoutGlobalScopes()->find($this->fieldId);
            if (! $field || $field->scope !== 'work_item') {
                return;
            }

            $items = WorkItem::query()
                ->withoutGlobalScopes()
                ->where('tenant_id', $this->tenantId)
                ->limit($this->limit)
                ->get(['id', 'tenant_id']);

            foreach ($items as $item) {
                GenerateAiFieldRunJob::dispatch(
                    $this->tenantId,
                    $field->getKey(),
                    $item->getKey(),
                    $this->actorId
                );
            }
        } finally {
            TenantContext::setTenant(null);
        }
    }
}
