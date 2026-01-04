<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreOfferRequest;
use Haida\MailtrapCore\Http\Requests\UpdateOfferRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapOfferResource;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\MailtrapCore\Services\MailtrapOfferService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfferController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapOffer::class, 'offer');
    }

    public function index(): AnonymousResourceCollection
    {
        $offers = MailtrapOffer::query()->latest()->paginate();

        return MailtrapOfferResource::collection($offers);
    }

    public function show(MailtrapOffer $offer): MailtrapOfferResource
    {
        return new MailtrapOfferResource($offer);
    }

    public function store(StoreOfferRequest $request, MailtrapOfferService $service): MailtrapOfferResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $offer = MailtrapOffer::query()->create($data);

        if ($request->boolean('publish_to_catalog')) {
            $service->publishToCatalog($offer);
        }

        return new MailtrapOfferResource($offer->refresh());
    }

    public function update(UpdateOfferRequest $request, MailtrapOffer $offer, MailtrapOfferService $service): MailtrapOfferResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $offer->update($data);

        if ($request->boolean('publish_to_catalog')) {
            $service->publishToCatalog($offer);
        }

        return new MailtrapOfferResource($offer->refresh());
    }

    public function destroy(MailtrapOffer $offer): array
    {
        $offer->delete();

        return ['status' => 'ok'];
    }
}
