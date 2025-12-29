<?php

namespace Haida\FilamentPettyCashIr\Policies\Concerns;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Database\Eloquent\Model;

trait HandlesPettyCashPermissions
{
    protected function allow(string $permission, ?Model $record = null): bool
    {
        return IamAuthorization::allows($permission, IamAuthorization::resolveTenantFromRecord($record));
    }
}
