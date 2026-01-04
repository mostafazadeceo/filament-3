<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait RequiresReason
{
    protected function resolveReason(Request $request, array $data = []): ?string
    {
        $reason = $data['reason'] ?? $request->input('reason');
        if (! $reason) {
            $header = config('filamat-iam.governance.reason_header', 'X-Change-Reason');
            $reason = $request->header($header);
        }

        $reason = is_string($reason) ? trim($reason) : null;

        return $reason !== '' ? $reason : null;
    }

    protected function ensureReason(Request $request, ?string $reason): ?Response
    {
        if (! (bool) config('filamat-iam.governance.require_reason', true)) {
            return null;
        }

        if ($reason) {
            return null;
        }

        return response(['message' => 'ثبت دلیل تغییر الزامی است.'], 422);
    }
}
