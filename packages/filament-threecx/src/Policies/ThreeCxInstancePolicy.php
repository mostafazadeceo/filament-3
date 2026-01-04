<?php

namespace Haida\FilamentThreeCx\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;

class ThreeCxInstancePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('threecx.view');
    }

    public function view(User $user, ThreeCxInstance $record): bool
    {
        return IamAuthorization::allows('threecx.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('threecx.manage');
    }

    public function update(User $user, ThreeCxInstance $record): bool
    {
        return IamAuthorization::allows('threecx.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, ThreeCxInstance $record): bool
    {
        return IamAuthorization::allows('threecx.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
