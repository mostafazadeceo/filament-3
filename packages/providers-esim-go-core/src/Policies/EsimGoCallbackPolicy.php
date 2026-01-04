<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoCallback;
use Illuminate\Contracts\Auth\Authenticatable;

class EsimGoCallbackPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('esim_go.webhook.view', null, $user);
    }

    public function view(?Authenticatable $user, EsimGoCallback $record): bool
    {
        return IamAuthorization::allows('esim_go.webhook.view', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
