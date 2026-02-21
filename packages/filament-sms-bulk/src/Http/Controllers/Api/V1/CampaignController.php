<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Services\Campaign\CampaignBuilderService;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Illuminate\Http\Request;

class CampaignController extends ApiController
{
    public function index()
    {
        return $this->ok(['items' => SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->latest('id')->paginate(50)->items()]);
    }

    public function store(Request $request, CampaignBuilderService $builder)
    {
        $payload = $request->validate([
            'provider_connection_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:150'],
            'mode' => ['required', 'string', 'max:32'],
            'language' => ['nullable', 'string', 'max:8'],
            'encoding' => ['nullable', 'string', 'max:16'],
            'sender' => ['required', 'string', 'max:32'],
            'message' => ['nullable', 'string'],
            'pattern_code' => ['nullable', 'string', 'max:128'],
            'quiet_hours_profile_id' => ['nullable', 'integer'],
            'schedule_at' => ['nullable', 'date'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'string'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'override_suppression' => ['nullable', 'boolean'],
            'cost_center' => ['nullable', 'string', 'max:64'],
        ]);

        $connection = SmsBulkProviderConnection::query()
            ->where('tenant_id', $this->tenantId())
            ->findOrFail((int) $payload['provider_connection_id']);

        try {
            $campaign = $builder->createDraft(
                connection: $connection,
                payload: $payload,
                recipientCount: count((array) $payload['recipients']),
                actorId: auth()->id(),
            );
        } catch (\Throwable $exception) {
            return response()->json([
                'data' => null,
                'meta' => [
                    'status' => false,
                    'message' => $exception->getMessage(),
                ],
            ], 422);
        }

        return $this->ok($campaign->toArray(), 201);
    }

    public function show(int $id)
    {
        $campaign = SmsBulkCampaign::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok($campaign->toArray());
    }
}
