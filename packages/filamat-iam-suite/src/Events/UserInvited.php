<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;

class UserInvited
{
    public function __construct(public readonly Authenticatable $user, public readonly ?Tenant $tenant = null) {}
}
