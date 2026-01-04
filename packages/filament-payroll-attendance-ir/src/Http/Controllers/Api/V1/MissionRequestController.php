<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreMissionRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateMissionRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\MissionRequestResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMission;

class MissionRequestController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollMission::class, 'mission_request');
    }

    public function index(): AnonymousResourceCollection
    {
        $requests = PayrollMission::query()->latest()->paginate();

        return MissionRequestResource::collection($requests);
    }

    public function show(PayrollMission $mission_request): MissionRequestResource
    {
        return new MissionRequestResource($mission_request);
    }

    public function store(StoreMissionRequest $request): MissionRequestResource
    {
        $payload = $request->validated();
        $payload['status'] = RequestStatus::Pending->value;

        $mission = PayrollMission::query()->create($payload);

        return new MissionRequestResource($mission);
    }

    public function update(UpdateMissionRequest $request, PayrollMission $mission_request): MissionRequestResource
    {
        $payload = $request->validated();

        if (isset($payload['status']) && in_array($payload['status'], [
            RequestStatus::Approved->value,
            RequestStatus::Rejected->value,
        ], true)) {
            $this->authorize('approve', $mission_request);
        }

        $payload = $this->applyApprovalMeta($payload);
        $mission_request->update($payload);

        return new MissionRequestResource($mission_request->refresh());
    }

    public function destroy(PayrollMission $mission_request): array
    {
        $mission_request->delete();

        return ['status' => 'ok'];
    }

    public function approve(PayrollMission $mission_request): MissionRequestResource
    {
        $this->authorize('approve', $mission_request);

        $mission_request->update([
            'status' => RequestStatus::Approved->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return new MissionRequestResource($mission_request->refresh());
    }

    public function reject(Request $request, PayrollMission $mission_request): MissionRequestResource
    {
        $this->authorize('approve', $mission_request);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $mission_request->update([
            'status' => RequestStatus::Rejected->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => trim(($mission_request->notes ?? '')."\n".($data['rejection_reason'] ?? '')),
        ]);

        return new MissionRequestResource($mission_request->refresh());
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyApprovalMeta(array $payload): array
    {
        if (! array_key_exists('status', $payload)) {
            return $payload;
        }

        if ($payload['status'] === RequestStatus::Approved->value) {
            $payload['approved_by'] = $payload['approved_by'] ?? auth()->id();
            $payload['approved_at'] = $payload['approved_at'] ?? now();
        }

        if ($payload['status'] === RequestStatus::Rejected->value) {
            $payload['approved_by'] = $payload['approved_by'] ?? auth()->id();
            $payload['approved_at'] = $payload['approved_at'] ?? now();
        }

        return $payload;
    }
}
