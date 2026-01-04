<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Tls;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class CertificateResult
{
    public function __construct(
        public readonly bool $issued,
        public readonly ?CarbonInterface $issuedAt,
        public readonly ?CarbonInterface $expiresAt,
        public readonly ?string $error,
    ) {
    }

    public static function issued(?CarbonInterface $issuedAt = null, ?CarbonInterface $expiresAt = null): self
    {
        return new self(true, $issuedAt ?? Carbon::now(), $expiresAt, null);
    }

    public static function failed(string $error): self
    {
        return new self(false, null, null, $error);
    }
}
