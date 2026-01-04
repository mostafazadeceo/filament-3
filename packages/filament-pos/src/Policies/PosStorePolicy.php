<?php

namespace Haida\FilamentPos\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPos\Models\PosStore;

class PosStorePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], null, $user);
    }

    public function view(User $user, PosStore $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('pos.manage_register', null, $user);
    }

    public function update(User $user, PosStore $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PosStore $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
