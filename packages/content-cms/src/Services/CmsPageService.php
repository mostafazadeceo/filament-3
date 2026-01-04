<?php

namespace Haida\ContentCms\Services;

use Haida\ContentCms\Models\CmsPage;
use Haida\ContentCms\Models\CmsPageRevision;
use Haida\PageBuilder\Services\PageBuilderService;
use Illuminate\Support\Facades\DB;

class CmsPageService
{
    public function __construct(private PageBuilderService $pageBuilder)
    {
    }

    public function publish(CmsPage $page, ?int $actorUserId = null): CmsPage
    {
        return DB::transaction(function () use ($page, $actorUserId): CmsPage {
            $payload = $page->draft_content ?? [];
            if (! is_array($payload)) {
                $payload = [];
            }

            $this->pageBuilder->validatePayload($payload);
            $payload = $this->pageBuilder->sanitizePayload($payload);

            $page->published_content = $payload;
            $page->status = 'published';
            $page->published_at = now();
            $page->updated_by_user_id = $actorUserId;
            $page->save();

            $this->createRevision($page, $payload, 'published', $actorUserId, 'انتشار صفحه');

            return $page;
        });
    }

    public function rollbackToPublished(CmsPage $page, ?int $actorUserId = null): CmsPage
    {
        return DB::transaction(function () use ($page, $actorUserId): CmsPage {
            $payload = $page->published_content ?? [];
            if (! is_array($payload)) {
                $payload = [];
            }

            $page->draft_content = $payload;
            $page->status = 'draft';
            $page->updated_by_user_id = $actorUserId;
            $page->save();

            $this->createRevision($page, $payload, 'rollback', $actorUserId, 'بازگشت به نسخه منتشر شده');

            return $page;
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function createRevision(
        CmsPage $page,
        array $payload,
        string $status,
        ?int $actorUserId,
        ?string $notes
    ): void {
        $version = CmsPageRevision::query()
            ->where('page_id', $page->getKey())
            ->max('version');

        CmsPageRevision::query()->create([
            'tenant_id' => $page->tenant_id,
            'page_id' => $page->getKey(),
            'version' => (int) ($version ?? 0) + 1,
            'status' => $status,
            'payload' => $payload,
            'published_at' => $status === 'published' ? now() : null,
            'created_by_user_id' => $actorUserId,
            'notes' => $notes,
        ]);
    }
}
