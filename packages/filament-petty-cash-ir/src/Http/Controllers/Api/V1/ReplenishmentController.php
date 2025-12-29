<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Http\Requests\StoreReplenishmentRequest;
use Haida\FilamentPettyCashIr\Http\Requests\UpdateReplenishmentRequest;
use Haida\FilamentPettyCashIr\Http\Resources\ReplenishmentResource;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReplenishmentController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PettyCashReplenishment::class, 'replenishment');
    }

    public function index(): AnonymousResourceCollection
    {
        $replenishments = PettyCashReplenishment::query()->latest()->paginate();

        return ReplenishmentResource::collection($replenishments);
    }

    public function show(PettyCashReplenishment $replenishment): ReplenishmentResource
    {
        return new ReplenishmentResource($replenishment);
    }

    public function store(StoreReplenishmentRequest $request): ReplenishmentResource
    {
        $replenishment = PettyCashReplenishment::query()->create($request->validated());

        return new ReplenishmentResource($replenishment);
    }

    public function update(UpdateReplenishmentRequest $request, PettyCashReplenishment $replenishment): ReplenishmentResource
    {
        $replenishment->update($request->validated());

        return new ReplenishmentResource($replenishment->refresh());
    }

    public function destroy(PettyCashReplenishment $replenishment): array
    {
        $replenishment->delete();

        return ['status' => 'ok'];
    }

    public function submit(PettyCashReplenishment $replenishment, PettyCashPostingService $service): ReplenishmentResource
    {
        $this->authorize('update', $replenishment);

        $replenishment = $service->submitReplenishment($replenishment, auth()->id());

        return new ReplenishmentResource($replenishment);
    }

    public function approve(PettyCashReplenishment $replenishment, PettyCashPostingService $service): ReplenishmentResource
    {
        $this->authorize('approve', $replenishment);

        $replenishment = $service->approveReplenishment($replenishment, auth()->id());

        return new ReplenishmentResource($replenishment);
    }

    public function reject(PettyCashReplenishment $replenishment, PettyCashPostingService $service): ReplenishmentResource
    {
        $this->authorize('reject', $replenishment);

        $replenishment = $service->rejectReplenishment($replenishment, auth()->id());

        return new ReplenishmentResource($replenishment);
    }

    public function post(PettyCashReplenishment $replenishment, PettyCashPostingService $service): ReplenishmentResource
    {
        $this->authorize('post', $replenishment);

        $replenishment = $service->postReplenishment($replenishment, auth()->id());

        return new ReplenishmentResource($replenishment);
    }
}
