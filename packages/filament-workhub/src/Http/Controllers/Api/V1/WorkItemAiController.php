<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkItemAiController extends ApiController
{
    public function personalSummary(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.use');

        $data = $request->validate([
            'include_comments' => ['nullable', 'boolean'],
        ]);

        $ttlMinutes = (int) config('filament-workhub.ai.personal_summary_ttl_minutes', 30);

        $payload = $service->summarizeWorkItem(
            $workItem->loadMissing(['project', 'status', 'assignee']),
            'personal_summary',
            'ttl',
            ['include_comments' => (bool) ($data['include_comments'] ?? true), 'ttl_minutes' => $ttlMinutes],
            $request->user()
        );

        return $this->summaryResponse($payload['result'], $payload['summary']);
    }

    public function sharedSummary(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.share');

        $data = $request->validate([
            'include_comments' => ['nullable', 'boolean'],
            'notify_watchers' => ['nullable', 'boolean'],
        ]);

        $payload = $service->summarizeWorkItem(
            $workItem->loadMissing(['project', 'status', 'assignee']),
            'shared_summary',
            'shared',
            ['include_comments' => (bool) ($data['include_comments'] ?? true)],
            $request->user()
        );

        return $this->summaryResponse($payload['result'], $payload['summary'], [
            'notify_watchers' => (bool) ($data['notify_watchers'] ?? false),
        ]);
    }

    public function threadSummary(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.use');

        $data = $request->validate([
            'ttl_minutes' => ['nullable', 'integer', 'min:5', 'max:1440'],
        ]);

        $ttlMinutes = (int) ($data['ttl_minutes'] ?? config('filament-workhub.ai.thread_summary_ttl_minutes', 60));

        $payload = $service->summarizeThread(
            $workItem,
            'ttl',
            ['ttl_minutes' => $ttlMinutes],
            $request->user()
        );

        return $this->summaryResponse($payload['result'], $payload['summary']);
    }

    public function generateSubtasks(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.use');

        $data = $request->validate([
            'max_items' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $payload = $service->suggestSubtasks(
            $workItem->loadMissing(['project', 'status', 'assignee']),
            (int) ($data['max_items'] ?? 8),
            $request->user()
        );

        if (! $payload['result']->ok) {
            return $this->aiError($payload['result']);
        }

        return response()->json([
            'ok' => true,
            'provider' => $payload['result']->provider,
            'model' => $payload['result']->model,
            'suggestions' => $payload['suggestions'],
        ]);
    }

    public function progressUpdate(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.use');

        $data = $request->validate([
            'window_days' => ['nullable', 'integer', 'in:1,7,30'],
        ]);

        $windowDays = (int) ($data['window_days'] ?? 7);
        $ttlMinutes = (int) config('filament-workhub.ai.progress_ttl_minutes', 30);

        $payload = $service->progressUpdate(
            $workItem,
            $windowDays,
            ['ttl_minutes' => $ttlMinutes],
            $request->user()
        );

        return $this->summaryResponse($payload['result'], $payload['summary'], ['window_days' => $windowDays]);
    }

    public function findSimilar(Request $request, WorkItem $workItem, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $workItem);
        $this->authorizeAi($workItem, 'workhub.ai.use');

        $data = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $limit = (int) ($data['limit'] ?? config('filament-workhub.ai.similarity_limit', 5));
        $items = $service->findSimilarTasks($workItem, $limit);

        return response()->json([
            'ok' => true,
            'items' => $items->map(fn (WorkItem $item) => WorkItemDto::fromModel($item)->toArray())->all(),
        ]);
    }

    protected function authorizeAi(WorkItem $workItem, string $permission): void
    {
        if (! IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($workItem))) {
            abort(403);
        }
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    protected function summaryResponse(AiResult $result, ?object $summary, array $extra = []): JsonResponse
    {
        if (! $result->ok) {
            return $this->aiError($result);
        }

        return response()->json(array_merge([
            'ok' => true,
            'provider' => $result->provider,
            'model' => $result->model,
            'summary_id' => $summary?->getKey(),
            'summary' => $summary?->summary_json ?? $result->output_json,
        ], $extra));
    }

    protected function aiError(AiResult $result): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'error' => $result->error,
            'warnings' => $result->warnings,
            'provider' => $result->provider,
        ], 422);
    }
}
