<?php

namespace Haida\FilamentPos\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPos\Models\PosCashierSession;

class PosCashierSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['pos.use', 'pos.manage_cash'], null, $user);
    }

    public function view(User $user, PosCashierSession $record): bool
    {
        return IamAuthorization::allowsAny(['pos.use', 'pos.manage_cash'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('pos.manage_cash', null, $user);
    }

    public function update(User $user, PosCashierSession $record): bool
    {
        return IamAuthorization::allows('pos.manage_cash', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PosCashierSession $record): bool
    {
        return IamAuthorization::allows('pos.manage_cash', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
