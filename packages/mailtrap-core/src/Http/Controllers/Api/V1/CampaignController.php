<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreCampaignRequest;
use Haida\MailtrapCore\Http\Requests\UpdateCampaignRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapCampaignResource;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Models\MailtrapCampaign;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapCampaignService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CampaignController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapCampaign::class, 'campaign');
    }

    public function index(): AnonymousResourceCollection
    {
        $campaigns = MailtrapCampaign::query()
            ->with(['audience', 'connection'])
            ->latest()
            ->paginate();

        return MailtrapCampaignResource::collection($campaigns);
    }

    public function show(MailtrapCampaign $campaign): MailtrapCampaignResource
    {
        return new MailtrapCampaignResource($campaign);
    }

    public function store(StoreCampaignRequest $request): MailtrapCampaignResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        MailtrapConnection::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('id', $data['connection_id'])
            ->firstOrFail();

        if (! empty($data['audience_id'])) {
            MailtrapAudience::query()
                ->where('tenant_id', $data['tenant_id'])
                ->where('id', $data['audience_id'])
                ->firstOrFail();
        }

        $campaign = MailtrapCampaign::query()->create($data);

        return new MailtrapCampaignResource($campaign);
    }

    public function update(UpdateCampaignRequest $request, MailtrapCampaign $campaign): MailtrapCampaignResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        if (! empty($data['connection_id'])) {
            MailtrapConnection::query()
                ->where('tenant_id', $campaign->tenant_id)
                ->where('id', $data['connection_id'])
                ->firstOrFail();
        }

        if (array_key_exists('audience_id', $data) && $data['audience_id']) {
            MailtrapAudience::query()
                ->where('tenant_id', $campaign->tenant_id)
                ->where('id', $data['audience_id'])
                ->firstOrFail();
        }

        $campaign->update($data);

        return new MailtrapCampaignResource($campaign->refresh());
    }

    public function destroy(MailtrapCampaign $campaign): array
    {
        $campaign->delete();

        return ['status' => 'ok'];
    }

    public function send(MailtrapCampaign $campaign, MailtrapCampaignService $service): array
    {
        $this->authorize('send', $campaign);

        $result = $service->dispatchWithSchedule($campaign);

        return ['status' => $result];
    }
}
