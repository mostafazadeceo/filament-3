<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\PrivacyEnforcer;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreTimeEventRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateTimeEventRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\TimeEventResource;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers\AttendanceCaptureDriverInterface;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers\HardwareDeviceDriver;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers\KioskDriver;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers\MobileDriver;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers\WebDriver;

class TimeEventController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(TimeEvent::class, 'time_event');
    }

    public function index(): AnonymousResourceCollection
    {
        $events = TimeEvent::query()->latest()->paginate();

        return TimeEventResource::collection($events);
    }

    public function show(TimeEvent $time_event): TimeEventResource
    {
        $this->logSensitiveAccess($time_event);

        return new TimeEventResource($time_event);
    }

    public function store(StoreTimeEventRequest $request): TimeEventResource
    {
        $payload = $request->validated();
        $driverKey = (string) ($payload['source'] ?? $request->input('driver', 'web'));
        $driver = $this->resolveDriver($driverKey);

        $event = $driver->capture($payload);

        return new TimeEventResource($event);
    }

    public function update(UpdateTimeEventRequest $request, TimeEvent $time_event): TimeEventResource
    {
        $payload = $request->validated();
        if (! array_key_exists('employee_id', $payload)) {
            $payload['employee_id'] = $time_event->employee_id;
        }
        $payload = app(PrivacyEnforcer::class)->sanitizeTimeEventPayload($payload);
        $time_event->update($payload);

        return new TimeEventResource($time_event->refresh());
    }

    public function destroy(TimeEvent $time_event): array
    {
        $time_event->delete();

        return ['status' => 'ok'];
    }

    private function resolveDriver(string $source): AttendanceCaptureDriverInterface
    {
        return match ($source) {
            'mobile' => app(MobileDriver::class),
            'kiosk' => app(KioskDriver::class),
            'hardware', 'device' => app(HardwareDeviceDriver::class),
            default => app(WebDriver::class),
        };
    }
}
