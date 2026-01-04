<?php

namespace Haida\FilamentPos\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPos\Models\PosRegister;

class PosRegisterPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], null, $user);
    }

    public function view(User $user, PosRegister $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('pos.manage_register', null, $user);
    }

    public function update(User $user, PosRegister $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PosRegister $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
