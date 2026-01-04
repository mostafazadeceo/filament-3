<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreAudienceRequest;
use Haida\MailtrapCore\Http\Requests\UpdateAudienceRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapAudienceResource;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AudienceController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapAudience::class, 'audience');
    }

    public function index(): AnonymousResourceCollection
    {
        $audiences = MailtrapAudience::query()
            ->withCount('contacts')
            ->latest()
            ->paginate();

        return MailtrapAudienceResource::collection($audiences);
    }

    public function show(MailtrapAudience $audience): MailtrapAudienceResource
    {
        return new MailtrapAudienceResource($audience->loadCount('contacts'));
    }

    public function store(StoreAudienceRequest $request): MailtrapAudienceResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $audience = MailtrapAudience::query()->create($data);

        return new MailtrapAudienceResource($audience->loadCount('contacts'));
    }

    public function update(UpdateAudienceRequest $request, MailtrapAudience $audience): MailtrapAudienceResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        $audience->update($data);

        return new MailtrapAudienceResource($audience->loadCount('contacts'));
    }

    public function destroy(MailtrapAudience $audience): array
    {
        $audience->delete();

        return ['status' => 'ok'];
    }
}
