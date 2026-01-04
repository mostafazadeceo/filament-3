<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\StoreEmployeeConsentRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Requests\UpdateEmployeeConsentRequest;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\EmployeeConsentResource;

class EmployeeConsentController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(EmployeeConsent::class, 'employee_consent');
    }

    public function index(): AnonymousResourceCollection
    {
        $consents = EmployeeConsent::query()->latest()->paginate();

        return EmployeeConsentResource::collection($consents);
    }

    public function show(EmployeeConsent $employee_consent): EmployeeConsentResource
    {
        $this->logSensitiveAccess($employee_consent);

        return new EmployeeConsentResource($employee_consent);
    }

    public function store(StoreEmployeeConsentRequest $request): EmployeeConsentResource
    {
        $payload = $this->mutateConsentPayload($request->validated());

        $consent = EmployeeConsent::query()->updateOrCreate(
            [
                'employee_id' => $payload['employee_id'],
                'consent_type' => $payload['consent_type'],
            ],
            $payload
        );

        return new EmployeeConsentResource($consent);
    }

    public function update(UpdateEmployeeConsentRequest $request, EmployeeConsent $employee_consent): EmployeeConsentResource
    {
        $payload = $this->mutateConsentPayload($request->validated());
        $employee_consent->update($payload);

        return new EmployeeConsentResource($employee_consent->refresh());
    }

    public function destroy(EmployeeConsent $employee_consent): array
    {
        $employee_consent->delete();

        return ['status' => 'ok'];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function mutateConsentPayload(array $payload): array
    {
        if (($payload['consent_type'] ?? null) === 'biometric_verification'
            && ! config('filament-payroll-attendance-ir.privacy.biometric_enabled', false)) {
            abort(422, 'Biometric consent disabled by config.');
        }

        if (! empty($payload['is_granted'])) {
            $payload['granted_by'] = $payload['granted_by'] ?? auth()->id();
            $payload['granted_at'] = $payload['granted_at'] ?? now();
            $payload['revoked_at'] = null;
        } elseif (array_key_exists('is_granted', $payload)) {
            $payload['revoked_at'] = $payload['revoked_at'] ?? now();
        }

        return $payload;
    }
}
