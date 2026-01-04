<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;
use Illuminate\Contracts\Auth\Authenticatable;

class EsimGoEsimPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('esim_go.fulfillment.view', null, $user);
    }

    public function view(?Authenticatable $user, EsimGoEsim $record): bool
    {
        return IamAuthorization::allows('esim_go.fulfillment.view', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
