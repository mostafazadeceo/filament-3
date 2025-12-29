<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\StoreProjectRequest;
use Haida\FilamentWorkhub\Http\Requests\UpdateProjectRequest;
use Haida\FilamentWorkhub\Http\Resources\ProjectResource;
use Haida\FilamentWorkhub\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectController extends ApiController
{
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()
            ->with(['workflow', 'lead'])
            ->paginate(50);

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        $this->authorize('create', Project::class);

        $data = $request->validated();
        $data['key'] = strtoupper((string) $data['key']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $project = Project::query()->create($data);

        return new ProjectResource($project);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        if (array_key_exists('key', $data)) {
            $data['key'] = strtoupper((string) $data['key']);
        }
        $data['updated_by'] = auth()->id();

        $project->update($data);

        return new ProjectResource($project->refresh());
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([], 204);
    }
}
