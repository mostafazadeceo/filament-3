<?php

namespace Haida\FilamentThreeCx\Exceptions;

class ThreeCxRateLimitException extends ThreeCxApiException
{
    public static function throttled(?int $statusCode = null, ?array $payload = null): self
    {
        return self::fromResponse($statusCode, $payload, 'محدودیت نرخ 3CX فعال است.');
    }
}
