<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoCatalogueSnapshot;
use Illuminate\Contracts\Auth\Authenticatable;

class EsimGoCatalogueSnapshotPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('esim_go.catalogue.view', null, $user);
    }

    public function view(?Authenticatable $user, EsimGoCatalogueSnapshot $record): bool
    {
        return IamAuthorization::allows('esim_go.catalogue.view', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
