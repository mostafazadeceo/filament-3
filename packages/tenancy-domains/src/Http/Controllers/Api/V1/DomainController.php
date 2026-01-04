<?php

namespace Haida\TenancyDomains\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Http\Requests\StoreDomainRequest;
use Haida\TenancyDomains\Http\Requests\UpdateDomainRequest;
use Haida\TenancyDomains\Http\Resources\DomainResource;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\SiteDomainService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DomainController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(SiteDomain::class, 'domain');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = SiteDomain::query()->latest();
        $tenant = TenantContext::getTenant();
        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        return DomainResource::collection($query->paginate());
    }

    public function show(SiteDomain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    public function store(StoreDomainRequest $request, SiteDomainService $service): DomainResource
    {
        $data = $request->validated();
        $data['tenant_id'] = TenantContext::getTenant()?->getKey();

        $domain = SiteDomain::query()->create($data);
        $service->requestVerification($domain, $data['verification_method'] ?? null);

        return new DomainResource($domain->refresh());
    }

    public function update(UpdateDomainRequest $request, SiteDomain $domain): DomainResource
    {
        $domain->update($request->validated());

        return new DomainResource($domain->refresh());
    }

    public function destroy(SiteDomain $domain): array
    {
        $domain->delete();

        return ['status' => 'ok'];
    }

    public function requestVerification(SiteDomain $domain, SiteDomainService $service): DomainResource
    {
        $domain = $service->requestVerification($domain);

        return new DomainResource($domain);
    }

    public function verify(SiteDomain $domain, SiteDomainService $service): DomainResource
    {
        $domain = $service->verify($domain);

        return new DomainResource($domain);
    }

    public function requestTls(SiteDomain $domain, SiteDomainService $service): DomainResource
    {
        $this->authorize('requestTls', $domain);

        $domain = $service->requestTls($domain);

        return new DomainResource($domain);
    }
}
