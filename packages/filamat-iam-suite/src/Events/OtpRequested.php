<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

use Illuminate\Contracts\Auth\Authenticatable;

class OtpRequested
{
    public function __construct(
        public readonly Authenticatable $user,
        public readonly string $purpose,
        public readonly string $code,
        public readonly array $meta = [],
    ) {}
}
