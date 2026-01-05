<?php

namespace Haida\PageBuilder\Services;

use Haida\PageBuilder\Models\PageTemplate;
use Haida\PageBuilder\Models\PageTemplateRevision;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PageBuilderService
{
    public function __construct(private HtmlSanitizer $sanitizer) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function sanitizePayload(array $payload): array
    {
        return $this->sanitizeArray($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function validatePayload(array $payload): void
    {
        if (! isset($payload['sections']) || ! is_array($payload['sections'])) {
            throw new InvalidArgumentException('ساختار صفحه نامعتبر است.');
        }

        foreach ($payload['sections'] as $section) {
            if (! is_array($section)) {
                throw new InvalidArgumentException('بخش ها باید آرایه باشند.');
            }

            if (empty($section['type']) || ! is_string($section['type'])) {
                throw new InvalidArgumentException('نوع بخش الزامی است.');
            }

            if (isset($section['blocks']) && ! is_array($section['blocks'])) {
                throw new InvalidArgumentException('بلوک ها باید آرایه باشند.');
            }

            if (isset($section['blocks']) && is_array($section['blocks'])) {
                foreach ($section['blocks'] as $block) {
                    if (! is_array($block) || empty($block['type']) || ! is_string($block['type'])) {
                        throw new InvalidArgumentException('نوع بلوک الزامی است.');
                    }
                }
            }
        }
    }

    public function publish(PageTemplate $template, ?int $actorUserId = null): PageTemplate
    {
        return DB::transaction(function () use ($template, $actorUserId): PageTemplate {
            $payload = $template->draft_content ?? [];
            if (! is_array($payload)) {
                $payload = [];
            }

            $this->validatePayload($payload);
            $payload = $this->sanitizePayload($payload);

            $template->published_content = $payload;
            $template->status = 'published';
            $template->published_at = now();
            $template->updated_by_user_id = $actorUserId;
            $template->save();

            $this->createRevision($template, $payload, 'published', $actorUserId, 'انتشار قالب');

            return $template;
        });
    }

    public function rollbackToPublished(PageTemplate $template, ?int $actorUserId = null): PageTemplate
    {
        return DB::transaction(function () use ($template, $actorUserId): PageTemplate {
            $payload = $template->published_content ?? [];
            if (! is_array($payload)) {
                $payload = [];
            }

            $template->draft_content = $payload;
            $template->status = 'draft';
            $template->updated_by_user_id = $actorUserId;
            $template->save();

            $this->createRevision($template, $payload, 'rollback', $actorUserId, 'بازگشت به نسخه منتشر شده');

            return $template;
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createRevision(
        PageTemplate $template,
        array $payload,
        string $status,
        ?int $actorUserId,
        ?string $notes
    ): void {
        $version = PageTemplateRevision::query()
            ->where('template_id', $template->getKey())
            ->max('version');

        PageTemplateRevision::query()->create([
            'tenant_id' => $template->tenant_id,
            'template_id' => $template->getKey(),
            'version' => (int) ($version ?? 0) + 1,
            'status' => $status,
            'payload' => $payload,
            'published_at' => $status === 'published' ? now() : null,
            'created_by_user_id' => $actorUserId,
            'notes' => $notes,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sanitizeArray(array $payload): array
    {
        $keys = $this->sanitizeKeys();
        $result = [];

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->sanitizeArray($value);

                continue;
            }

            if (is_string($value) && $this->shouldSanitizeKey((string) $key, $keys)) {
                $result[$key] = $this->sanitizer->sanitize($value);

                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return array<int, string>
     */
    private function sanitizeKeys(): array
    {
        return (array) config('page-builder.sanitize.sanitize_keys', []);
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function shouldSanitizeKey(string $key, array $keys): bool
    {
        if (in_array($key, $keys, true)) {
            return true;
        }

        return str_ends_with($key, '_html') || str_ends_with($key, '_rich_text');
    }
}
