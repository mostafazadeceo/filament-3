<?php

namespace Haida\FilamentStorefrontBuilder\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentStorefrontBuilder\Models\StoreTheme;

class StoreThemePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['storebuilder.view', 'storebuilder.manage'], null, $user);
    }

    public function view(User $user, StoreTheme $record): bool
    {
        return IamAuthorization::allowsAny(['storebuilder.view', 'storebuilder.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('storebuilder.manage', null, $user);
    }

    public function update(User $user, StoreTheme $record): bool
    {
        return IamAuthorization::allows('storebuilder.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, StoreTheme $record): bool
    {
        return IamAuthorization::allows('storebuilder.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
