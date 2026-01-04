<?php

namespace Haida\FilamentPos\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPos\Models\PosSale;

class PosSalePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use'], null, $user);
    }

    public function view(User $user, PosSale $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('pos.use', null, $user);
    }

    public function update(User $user, PosSale $record): bool
    {
        return IamAuthorization::allows('pos.use', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PosSale $record): bool
    {
        return IamAuthorization::allows('pos.void', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function refund(User $user, PosSale $record): bool
    {
        return IamAuthorization::allows('pos.refund', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function void(User $user, PosSale $record): bool
    {
        return IamAuthorization::allows('pos.void', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function overrideDiscount(User $user, PosSale $record): bool
    {
        return IamAuthorization::allows('pos.override_discount', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
