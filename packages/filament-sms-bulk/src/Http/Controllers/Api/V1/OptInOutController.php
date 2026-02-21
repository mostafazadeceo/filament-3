<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Jobs\ApplyOptOutJob;
use Haida\SmsBulk\Services\SuppressionService;
use Illuminate\Http\Request;

class OptInOutController extends ApiController
{
    public function optOut(Request $request)
    {
        $data = $request->validate([
            'msisdn' => ['required', 'string', 'max:32'],
            'source' => ['nullable', 'string', 'max:32'],
            'async' => ['nullable', 'boolean'],
        ]);

        if ((bool) ($data['async'] ?? false)) {
            ApplyOptOutJob::dispatch(
                tenantId: $this->tenantId(),
                msisdn: $data['msisdn'],
                source: (string) ($data['source'] ?? 'api'),
                actorId: auth()->id(),
            );
        } else {
            app(SuppressionService::class)->applyOptOut(
                tenantId: $this->tenantId(),
                msisdn: $data['msisdn'],
                source: (string) ($data['source'] ?? 'api'),
                actorId: auth()->id(),
            );
        }

        return $this->ok(['status' => 'opted_out']);
    }

    public function optIn(Request $request, SuppressionService $suppression)
    {
        $data = $request->validate([
            'msisdn' => ['required', 'string', 'max:32'],
            'source' => ['nullable', 'string', 'max:32'],
        ]);

        $suppression->applyOptIn(
            tenantId: $this->tenantId(),
            msisdn: $data['msisdn'],
            source: (string) ($data['source'] ?? 'api'),
            actorId: auth()->id(),
        );

        return $this->ok(['status' => 'opted_in']);
    }
}
