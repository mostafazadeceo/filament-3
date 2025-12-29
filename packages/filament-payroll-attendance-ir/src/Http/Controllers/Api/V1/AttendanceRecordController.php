<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreAttendanceRecordRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateAttendanceRecordRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\AttendanceRecordResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollWebhookService;

class AttendanceRecordController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollAttendanceRecord::class, 'attendance_record');
    }

    public function index(): AnonymousResourceCollection
    {
        $records = PayrollAttendanceRecord::query()->latest()->paginate();

        return AttendanceRecordResource::collection($records);
    }

    public function show(PayrollAttendanceRecord $attendance_record): AttendanceRecordResource
    {
        return new AttendanceRecordResource($attendance_record);
    }

    public function store(StoreAttendanceRecordRequest $request): AttendanceRecordResource
    {
        $record = PayrollAttendanceRecord::query()->create($request->validated());

        return new AttendanceRecordResource($record);
    }

    public function update(UpdateAttendanceRecordRequest $request, PayrollAttendanceRecord $attendance_record): AttendanceRecordResource
    {
        $attendance_record->update($request->validated());

        return new AttendanceRecordResource($attendance_record->refresh());
    }

    public function destroy(PayrollAttendanceRecord $attendance_record): array
    {
        $attendance_record->delete();

        return ['status' => 'ok'];
    }

    public function approve(PayrollAttendanceRecord $attendance_record): AttendanceRecordResource
    {
        $this->authorize('approve', $attendance_record);

        $attendance_record->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        app(PayrollWebhookService::class)->dispatch('attendance.approved', $attendance_record->company_id, [
            'id' => $attendance_record->getKey(),
            'employee_id' => $attendance_record->employee_id,
            'work_date' => $attendance_record->work_date,
            'status' => $attendance_record->status,
        ]);

        return new AttendanceRecordResource($attendance_record->refresh());
    }
}
