<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCryptoGateway\Http\Requests\StorePayoutDestinationRequest;
use Haida\FilamentCryptoGateway\Http\Requests\UpdatePayoutDestinationRequest;
use Haida\FilamentCryptoGateway\Http\Resources\CryptoPayoutDestinationResource;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PayoutDestinationController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(CryptoPayoutDestination::class, 'destination');
    }

    public function index(): AnonymousResourceCollection
    {
        $tenantId = (int) TenantContext::getTenantId();
        $destinations = CryptoPayoutDestination::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();

        return CryptoPayoutDestinationResource::collection($destinations);
    }

    public function store(StorePayoutDestinationRequest $request): CryptoPayoutDestinationResource
    {
        $payload = $request->validated();
        $payload['tenant_id'] = (int) TenantContext::getTenantId();
        $payload['status'] = $payload['status'] ?? 'active';

        if ($payload['status'] === 'active' && empty($payload['approved_at'])) {
            $payload['approved_at'] = now();
            $payload['approved_by'] = Auth::id();
        }

        $destination = CryptoPayoutDestination::query()->create($payload);

        return new CryptoPayoutDestinationResource($destination);
    }

    public function show(CryptoPayoutDestination $destination): CryptoPayoutDestinationResource
    {
        return new CryptoPayoutDestinationResource($destination);
    }

    public function update(UpdatePayoutDestinationRequest $request, CryptoPayoutDestination $destination): CryptoPayoutDestinationResource
    {
        $payload = $request->validated();

        if (($payload['status'] ?? null) === 'active' && ! $destination->approved_at) {
            $payload['approved_at'] = now();
            $payload['approved_by'] = Auth::id();
        }

        $destination->update($payload);

        return new CryptoPayoutDestinationResource($destination->refresh());
    }

    public function destroy(CryptoPayoutDestination $destination): JsonResponse
    {
        $destination->delete();

        return response()->json(['status' => 'ok']);
    }
}
