<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\MailtrapCore\Http\Requests\StoreInboxRequest;
use Haida\MailtrapCore\Http\Requests\SyncInboxRequest;
use Haida\MailtrapCore\Http\Requests\UpdateInboxRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapInboxResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InboxController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapInbox::class, 'inbox');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = MailtrapInbox::query()->latest();

        if ($connectionId = request('connection_id')) {
            $query->where('connection_id', $connectionId);
        }

        return MailtrapInboxResource::collection($query->paginate());
    }

    public function sync(SyncInboxRequest $request, MailtrapInboxService $service): array
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return ['count' => 0];
        }

        $connectionId = (int) $request->validated('connection_id');
        $connection = MailtrapConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('id', $connectionId)
            ->firstOrFail();

        $count = $service->sync($connection, (bool) $request->validated('force', false));

        return ['count' => $count];
    }

    public function store(StoreInboxRequest $request, MailtrapInboxService $service): MailtrapInboxResource
    {
        $data = $request->validated();

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('id', $data['connection_id'])
            ->firstOrFail();

        $inbox = $service->create($connection, [
            'name' => $data['name'],
            'status' => $data['status'] ?? null,
        ]);

        return new MailtrapInboxResource($inbox);
    }

    public function update(UpdateInboxRequest $request, MailtrapInbox $inbox, MailtrapInboxService $service): MailtrapInboxResource
    {
        $data = $request->validated();

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $inbox->tenant_id)
            ->where('id', $inbox->connection_id)
            ->firstOrFail();

        $updated = $service->update($connection, $inbox, $data);

        return new MailtrapInboxResource($updated);
    }

    public function destroy(MailtrapInbox $inbox, MailtrapInboxService $service): array
    {
        $connection = MailtrapConnection::query()
            ->where('tenant_id', $inbox->tenant_id)
            ->where('id', $inbox->connection_id)
            ->firstOrFail();

        $service->delete($connection, $inbox);

        return ['status' => 'ok'];
    }
}
