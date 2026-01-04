<?php

namespace Haida\FilamentThreeCx\Exceptions;

class ThreeCxAuthException extends ThreeCxApiException
{
    public static function authFailed(?int $statusCode = null, ?array $payload = null): self
    {
        return self::fromResponse($statusCode, $payload, 'احراز هویت 3CX ناموفق بود.');
    }
}
