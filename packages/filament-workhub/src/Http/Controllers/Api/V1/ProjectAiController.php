<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectAiController extends ApiController
{
    public function executiveSummary(Request $request, Project $project, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $project);
        $this->authorizeAi($project, 'workhub.ai.project_reports.manage');

        $filters = $request->validate([
            'status_id' => ['nullable', 'integer'],
            'updated_since_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $payload = $service->generateExecutiveSummary($project, $filters, $request->user());

        if (! $payload['result']->ok) {
            return $this->aiError($payload['result']);
        }

        return response()->json([
            'ok' => true,
            'provider' => $payload['result']->provider,
            'model' => $payload['result']->model,
            'report' => [
                'id' => $payload['report']?->getKey(),
                'status' => $payload['report']?->status_enum,
                'body_markdown' => $payload['report']?->body_markdown,
                'project' => ProjectDto::fromModel($project)->toArray(),
            ],
        ]);
    }

    public function stuckTasks(Request $request, Project $project, WorkhubAiService $service): JsonResponse
    {
        $this->authorize('view', $project);
        $this->authorizeAi($project, 'workhub.ai.project_reports.manage');

        $data = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $days = (int) ($data['days'] ?? config('filament-workhub.ai.stuck_days', 7));
        $items = $service->stuckTasks($project, $days);

        return response()->json([
            'ok' => true,
            'items' => $items->map(fn (WorkItem $item) => WorkItemDto::fromModel($item)->toArray())->all(),
        ]);
    }

    protected function authorizeAi(Project $project, string $permission): void
    {
        if (! IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($project))) {
            abort(403);
        }
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
