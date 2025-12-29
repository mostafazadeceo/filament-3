<?php

namespace Vendor\FilamentAccountingIr\Policies\Concerns;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Database\Eloquent\Model;

trait HandlesAccountingPermissions
{
    protected function allow(string $permission, ?Model $record = null): bool
    {
        return IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($record));
    }
}
