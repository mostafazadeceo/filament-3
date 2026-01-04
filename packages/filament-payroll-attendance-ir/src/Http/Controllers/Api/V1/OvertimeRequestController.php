<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreOvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateOvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\OvertimeRequestResource;

class OvertimeRequestController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(OvertimeRequest::class, 'overtime_request');
    }

    public function index(): AnonymousResourceCollection
    {
        $requests = OvertimeRequest::query()->latest()->paginate();

        return OvertimeRequestResource::collection($requests);
    }

    public function show(OvertimeRequest $overtime_request): OvertimeRequestResource
    {
        return new OvertimeRequestResource($overtime_request);
    }

    public function store(StoreOvertimeRequest $request): OvertimeRequestResource
    {
        $payload = $request->validated();
        $payload['status'] = RequestStatus::Pending->value;
        $payload['requested_by'] = $payload['requested_by'] ?? auth()->id();

        $overtime = OvertimeRequest::query()->create($payload);

        return new OvertimeRequestResource($overtime);
    }

    public function update(UpdateOvertimeRequest $request, OvertimeRequest $overtime_request): OvertimeRequestResource
    {
        $payload = $request->validated();

        if (isset($payload['status']) && in_array($payload['status'], [
            RequestStatus::Approved->value,
            RequestStatus::Rejected->value,
        ], true)) {
            $this->authorize('approve', $overtime_request);
        }

        $payload = $this->applyApprovalMeta($payload);
        $overtime_request->update($payload);

        return new OvertimeRequestResource($overtime_request->refresh());
    }

    public function destroy(OvertimeRequest $overtime_request): array
    {
        $overtime_request->delete();

        return ['status' => 'ok'];
    }

    public function approve(OvertimeRequest $overtime_request): OvertimeRequestResource
    {
        $this->authorize('approve', $overtime_request);

        $overtime_request->update([
            'status' => RequestStatus::Approved->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return new OvertimeRequestResource($overtime_request->refresh());
    }

    public function reject(Request $request, OvertimeRequest $overtime_request): OvertimeRequestResource
    {
        $this->authorize('approve', $overtime_request);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $overtime_request->update([
            'status' => RequestStatus::Rejected->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'metadata' => array_merge((array) ($overtime_request->metadata ?? []), [
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]),
        ]);

        return new OvertimeRequestResource($overtime_request->refresh());
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
