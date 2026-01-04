<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreAudienceContactRequest;
use Haida\MailtrapCore\Http\Requests\UpdateAudienceContactRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapAudienceContactResource;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Models\MailtrapAudienceContact;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AudienceContactController extends ApiController
{
    public function index(MailtrapAudience $audience): AnonymousResourceCollection
    {
        $this->authorize('view', $audience);

        $contacts = MailtrapAudienceContact::query()
            ->where('audience_id', $audience->getKey())
            ->latest()
            ->paginate();

        return MailtrapAudienceContactResource::collection($contacts);
    }

    public function store(StoreAudienceContactRequest $request, MailtrapAudience $audience): MailtrapAudienceContactResource
    {
        $this->authorize('update', $audience);

        $data = $request->validated();
        $data['tenant_id'] = $audience->tenant_id;
        $data['audience_id'] = $audience->getKey();

        if (($data['status'] ?? 'subscribed') === 'unsubscribed') {
            $data['unsubscribed_at'] = $data['unsubscribed_at'] ?? now();
        } else {
            $data['unsubscribed_at'] = null;
        }

        $contact = MailtrapAudienceContact::query()->create($data);

        return new MailtrapAudienceContactResource($contact);
    }

    public function update(UpdateAudienceContactRequest $request, MailtrapAudience $audience, MailtrapAudienceContact $contact): MailtrapAudienceContactResource
    {
        $this->authorize('update', $audience);

        if ($contact->audience_id !== $audience->getKey()) {
            abort(404);
        }

        $data = $request->validated();

        if (($data['status'] ?? $contact->status) === 'unsubscribed') {
            $data['unsubscribed_at'] = $contact->unsubscribed_at ?? now();
        } else {
            $data['unsubscribed_at'] = null;
        }

        $contact->update($data);

        return new MailtrapAudienceContactResource($contact->refresh());
    }

    public function destroy(MailtrapAudience $audience, MailtrapAudienceContact $contact): array
    {
        $this->authorize('update', $audience);

        if ($contact->audience_id !== $audience->getKey()) {
            abort(404);
        }

        $contact->delete();

        return ['status' => 'ok'];
    }
}
