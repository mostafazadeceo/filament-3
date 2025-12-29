<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies\Concerns;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Database\Eloquent\Model;

trait HandlesPayrollPermissions
{
    protected function allow(string $permission, ?Model $record = null): bool
    {
        return IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($record));
    }
}
