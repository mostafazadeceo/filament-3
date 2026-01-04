<?php

namespace Haida\FilamentThreeCx\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentThreeCx\Models\ThreeCxApiAuditLog;

class ThreeCxApiAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('threecx.manage');
    }

    public function view(User $user, ThreeCxApiAuditLog $record): bool
    {
        return IamAuthorization::allows('threecx.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
