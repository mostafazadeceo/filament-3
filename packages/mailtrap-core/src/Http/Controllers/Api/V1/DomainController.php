<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\MailtrapCore\Http\Requests\StoreDomainRequest;
use Haida\MailtrapCore\Http\Requests\SyncDomainRequest;
use Haida\MailtrapCore\Http\Requests\UpdateDomainRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapSendingDomainResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;
use Haida\MailtrapCore\Services\MailtrapDomainService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DomainController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapSendingDomain::class, 'domain');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = MailtrapSendingDomain::query()->latest();

        if ($connectionId = request('connection_id')) {
            $query->where('connection_id', $connectionId);
        }

        return MailtrapSendingDomainResource::collection($query->paginate());
    }

    public function sync(SyncDomainRequest $request, MailtrapDomainService $service): array
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

    public function store(StoreDomainRequest $request, MailtrapDomainService $service): MailtrapSendingDomainResource
    {
        $data = $request->validated();

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('id', $data['connection_id'])
            ->firstOrFail();

        $domain = $service->create($connection, [
            'domain_name' => $data['domain_name'],
        ]);

        return new MailtrapSendingDomainResource($domain);
    }

    public function update(UpdateDomainRequest $request, MailtrapSendingDomain $domain, MailtrapDomainService $service): MailtrapSendingDomainResource
    {
        $data = $request->validated();

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $domain->tenant_id)
            ->where('id', $domain->connection_id)
            ->firstOrFail();

        $updated = $service->update($connection, $domain, $data);

        return new MailtrapSendingDomainResource($updated);
    }

    public function destroy(MailtrapSendingDomain $domain, MailtrapDomainService $service): array
    {
        $connection = MailtrapConnection::query()
            ->where('tenant_id', $domain->tenant_id)
            ->where('id', $domain->connection_id)
            ->firstOrFail();

        $service->delete($connection, $domain);

        return ['status' => 'ok'];
    }
}
