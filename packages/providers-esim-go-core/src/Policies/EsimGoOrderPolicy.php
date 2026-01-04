<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;

class EsimGoOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('esim_go.order.view');
    }

    public function view(User $user, EsimGoOrder $record): bool
    {
        return IamAuthorization::allows('esim_go.order.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('esim_go.order.manage');
    }

    public function update(User $user, EsimGoOrder $record): bool
    {
        return IamAuthorization::allows('esim_go.order.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, EsimGoOrder $record): bool
    {
        return IamAuthorization::allows('esim_go.order.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
