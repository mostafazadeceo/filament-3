<?php

namespace Haida\FilamentThreeCx\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Contracts\ContactDirectoryInterface;
use Haida\FilamentThreeCx\Http\Requests\ThreeCxCrmContactRequest;
use Haida\FilamentThreeCx\Http\Requests\ThreeCxCrmJournalCallRequest;
use Haida\FilamentThreeCx\Http\Requests\ThreeCxCrmJournalChatRequest;
use Haida\FilamentThreeCx\Http\Requests\ThreeCxCrmLookupRequest;
use Haida\FilamentThreeCx\Http\Requests\ThreeCxCrmSearchRequest;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxEventDispatcher;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CrmController extends ApiController
{
    public function lookup(ThreeCxCrmLookupRequest $request, ContactDirectoryInterface $directory): JsonResponse
    {
        $instance = $this->resolveInstance($request);
        $data = $directory->lookup(
            $instance,
            $request->validated('phone'),
            $request->validated('email')
        );

        return response()->json(['data' => $data]);
    }

    public function search(ThreeCxCrmSearchRequest $request, ContactDirectoryInterface $directory): JsonResponse
    {
        $instance = $this->resolveInstance($request);
        $data = $directory->search($instance, (string) $request->validated('query'));

        return response()->json(['data' => $data]);
    }

    public function storeContact(ThreeCxCrmContactRequest $request, ContactDirectoryInterface $directory): JsonResponse
    {
        $instance = $this->resolveInstance($request);
        $contact = $directory->create($instance, $request->validated());

        return response()->json(['data' => $contact], 201);
    }

    public function journalCall(ThreeCxCrmJournalCallRequest $request, ThreeCxEventDispatcher $events): JsonResponse
    {
        $instance = $this->resolveInstance($request);
        $payload = $request->all();

        $log = ThreeCxCallLog::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'direction' => $this->normalizeString($payload['direction'] ?? null),
            'from_number' => $this->normalizeString($payload['from_number'] ?? $payload['from'] ?? null),
            'to_number' => $this->normalizeString($payload['to_number'] ?? $payload['to'] ?? null),
            'started_at' => $this->parseDate($payload['started_at'] ?? $payload['startedAt'] ?? null),
            'ended_at' => $this->parseDate($payload['ended_at'] ?? $payload['endedAt'] ?? null),
            'duration' => isset($payload['duration']) ? (int) $payload['duration'] : null,
            'status' => $this->normalizeString($payload['status'] ?? null),
            'external_id' => $this->normalizeString($payload['external_id'] ?? $payload['id'] ?? null),
            'raw_payload' => $this->shouldStoreRaw('call') ? $payload : null,
        ]);

        if ($this->isMissedStatus($log->status)) {
            $events->dispatchMissedCall($log);
        }

        return response()->json(['data' => ['id' => $log->getKey()]], 201);
    }

    public function journalChat(ThreeCxCrmJournalChatRequest $request): JsonResponse
    {
        $instance = $this->resolveInstance($request);
        $payload = $request->all();

        $log = ThreeCxCallLog::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'direction' => $this->normalizeString($payload['direction'] ?? 'chat'),
            'from_number' => $this->normalizeString($payload['from_number'] ?? $payload['from'] ?? null),
            'to_number' => $this->normalizeString($payload['to_number'] ?? $payload['to'] ?? null),
            'started_at' => $this->parseDate($payload['started_at'] ?? $payload['startedAt'] ?? null),
            'ended_at' => $this->parseDate($payload['ended_at'] ?? $payload['endedAt'] ?? null),
            'duration' => isset($payload['duration']) ? (int) $payload['duration'] : null,
            'status' => $this->normalizeString($payload['status'] ?? 'chat'),
            'external_id' => $this->normalizeString($payload['external_id'] ?? $payload['id'] ?? null),
            'raw_payload' => $this->shouldStoreRaw('chat') ? $payload : null,
        ]);

        return response()->json(['data' => ['id' => $log->getKey()]], 201);
    }

    protected function resolveInstance(Request $request): ThreeCxInstance
    {
        $attached = $request->attributes->get('threecx_instance');
        if ($attached instanceof ThreeCxInstance) {
            return $attached;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            throw new HttpResponseException(response()->json(['message' => 'فضای کاری مشخص نیست.'], 401));
        }

        $instanceParam = (string) config('filament-threecx.crm_connector.instance_param', 'instance_id');
        $instanceId = $request->query($instanceParam);

        $query = ThreeCxInstance::query()->where('tenant_id', $tenantId);
        if ($instanceId) {
            $query->where('id', $instanceId);
        } else {
            $query->where('crm_connector_enabled', true)->orderByDesc('id');
        }

        $instance = $query->first();
        if (! $instance) {
            throw new HttpResponseException(response()->json(['message' => 'اتصال 3CX یافت نشد.'], 404));
        }

        if (! $instance->crm_connector_enabled) {
            throw new HttpResponseException(response()->json(['message' => 'اتصال 3CX غیرفعال است.'], 403));
        }

        return $instance;
    }

    protected function normalizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function shouldStoreRaw(string $type): bool
    {
        if ($type === 'call') {
            return (bool) config('filament-threecx.crm_connector.store_call_raw_payload', false);
        }

        if ($type === 'chat') {
            return (bool) config('filament-threecx.crm_connector.store_chat_raw_payload', false);
        }

        return (bool) config('filament-threecx.crm_connector.store_raw_payload', false);
    }

    protected function isMissedStatus(?string $value): bool
    {
        $value = strtolower((string) $value);

        return in_array($value, ['missed', 'no_answer', 'noanswer', 'unanswered'], true);
    }
}
