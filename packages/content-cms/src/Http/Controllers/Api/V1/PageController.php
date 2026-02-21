<?php

namespace Haida\ContentCms\Http\Controllers\Api\V1;

use Haida\ContentCms\Http\Requests\StorePageRequest;
use Haida\ContentCms\Http\Requests\UpdatePageRequest;
use Haida\ContentCms\Http\Resources\PageResource;
use Haida\ContentCms\Models\CmsPage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends ApiController
{
    public function __construct()
    {
        // API keys are used for server-to-server sync (no user session). For those requests
        // we rely on `filamat-iam.scope:*` middleware instead of per-user policies.
        $apiKeyHeader = (string) config('filamat-iam.api.api_key_header', 'X-Api-Key');
        if (! request()->header($apiKeyHeader)) {
            $this->authorizeResource(CmsPage::class, 'page');
        }
    }

    public function index(): AnonymousResourceCollection
    {
        $pages = CmsPage::query()
            ->with('site')
            ->latest()
            ->paginate();

        return PageResource::collection($pages);
    }

    public function show(CmsPage $page): PageResource
    {
        return new PageResource($page->loadMissing('site'));
    }

    public function store(StorePageRequest $request): PageResource
    {
        $page = CmsPage::query()->create($request->validated());

        return new PageResource($page->loadMissing('site'));
    }

    public function update(UpdatePageRequest $request, CmsPage $page): PageResource
    {
        $page->update($request->validated());

        return new PageResource($page->refresh()->loadMissing('site'));
    }

    public function destroy(CmsPage $page): array
    {
        $page->delete();

        return ['status' => 'ok'];
    }
}
