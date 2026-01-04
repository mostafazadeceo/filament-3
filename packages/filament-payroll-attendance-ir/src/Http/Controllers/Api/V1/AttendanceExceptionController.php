<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ResolveException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\AttendanceExceptionResource;

class AttendanceExceptionController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(AttendanceException::class, 'attendance_exception');
    }

    public function index(): AnonymousResourceCollection
    {
        $exceptions = AttendanceException::query()->latest()->paginate();

        return AttendanceExceptionResource::collection($exceptions);
    }

    public function show(AttendanceException $attendance_exception): AttendanceExceptionResource
    {
        $this->logSensitiveAccess($attendance_exception);

        return new AttendanceExceptionResource($attendance_exception);
    }

    public function resolve(AttendanceException $attendance_exception): AttendanceExceptionResource
    {
        $this->authorize('resolve', $attendance_exception);

        $payload = request()->validate([
            'resolution_notes' => ['nullable', 'string'],
            'resolved_at' => ['nullable', 'date'],
            'resolved_by' => ['nullable', 'integer'],
        ]);

        $resolved = app(ResolveException::class)->execute($attendance_exception, $payload);

        return new AttendanceExceptionResource($resolved);
    }
}
