<?php

namespace Haida\FilamentPos\Http\Requests;

class SyncOutboxRequest extends BasePosRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $max = (int) config('filament-pos.offline.max_outbox_batch', 200);

        return [
            'device_id' => ['nullable', 'integer'],
            'events' => ['required', 'array', 'max:'.$max],
            'events.*.event_type' => ['required', 'string', 'max:64'],
            'events.*.event_id' => ['nullable', 'string', 'max:128'],
            'events.*.idempotency_key' => ['nullable', 'string', 'max:255'],
            'events.*.payload' => ['required', 'array'],
        ];
    }
}
