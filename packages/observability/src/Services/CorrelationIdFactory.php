<?php

declare(strict_types=1);

namespace Haida\Observability\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorrelationIdFactory
{
    public function fromRequest(Request $request, string $header): string
    {
        $value = $request->headers->get($header);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return (string) Str::orderedUuid();
    }
}
