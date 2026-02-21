<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class SmsBulkModelPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return IamAuthorization::allowsAny([
            'sms-bulk.view',
            'sms-bulk.manage',
        ], null, $user);
    }

    public function view(Authenticatable $user, mixed $record): bool
    {
        return IamAuthorization::allowsAny([
            'sms-bulk.view',
            'sms-bulk.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(Authenticatable $user): bool
    {
        return IamAuthorization::allows('sms-bulk.manage', null, $user);
    }

    public function update(Authenticatable $user, mixed $record): bool
    {
        return IamAuthorization::allows('sms-bulk.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(Authenticatable $user, mixed $record): bool
    {
        return IamAuthorization::allows('sms-bulk.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
