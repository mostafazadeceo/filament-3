<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreProjectRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateProjectRequest;
use Vendor\FilamentAccountingIr\Http\Resources\ProjectResource;
use Vendor\FilamentAccountingIr\Models\Project;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()->latest()->paginate();

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        $project = Project::query()->create($request->validated());

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Project $project): array
    {
        $project->delete();

        return ['status' => 'ok'];
    }
}
