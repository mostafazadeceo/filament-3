<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Jobs\EnqueueCampaignJob;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Illuminate\Http\Request;

class CampaignActionController extends ApiController
{
    public function submit(int $id)
    {
        $campaign = SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        EnqueueCampaignJob::dispatch($this->tenantId(), (int) $campaign->getKey());

        return $this->ok(['queued' => true]);
    }

    public function pause(int $id)
    {
        $campaign = SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $campaign->update(['status' => 'paused']);

        return $this->ok(['status' => 'paused']);
    }

    public function resume(int $id)
    {
        $campaign = SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $campaign->update(['status' => 'queued']);

        return $this->ok(['status' => 'queued']);
    }

    public function cancel(int $id)
    {
        $campaign = SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $campaign->update(['status' => 'cancelled']);

        return $this->ok(['status' => 'cancelled']);
    }
}
