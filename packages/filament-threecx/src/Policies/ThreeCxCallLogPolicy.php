<?php

namespace Haida\FilamentThreeCx\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;

class ThreeCxCallLogPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('threecx.view');
    }

    public function view(User $user, ThreeCxCallLog $record): bool
    {
        return IamAuthorization::allows('threecx.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ThreeCxCallLog $record): bool
    {
        return false;
    }

    public function delete(User $user, ThreeCxCallLog $record): bool
    {
        return false;
    }
}
