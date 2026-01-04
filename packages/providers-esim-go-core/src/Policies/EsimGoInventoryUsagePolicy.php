<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoInventoryUsage;
use Illuminate\Contracts\Auth\Authenticatable;

class EsimGoInventoryUsagePolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('esim_go.inventory.view', null, $user);
    }

    public function view(?Authenticatable $user, EsimGoInventoryUsage $record): bool
    {
        return IamAuthorization::allows('esim_go.inventory.view', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
