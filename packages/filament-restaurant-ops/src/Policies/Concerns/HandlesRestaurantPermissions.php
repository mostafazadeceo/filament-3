<?php

namespace Haida\FilamentRestaurantOps\Policies\Concerns;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Database\Eloquent\Model;

trait HandlesRestaurantPermissions
{
    protected function allow(string $permission, ?Model $record = null): bool
    {
        return IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($record));
    }
}
