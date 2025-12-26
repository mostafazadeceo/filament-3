<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

use Filamat\IamSuite\Models\Tenant;

class TenantCreated
{
    public function __construct(public readonly Tenant $tenant) {}
}
