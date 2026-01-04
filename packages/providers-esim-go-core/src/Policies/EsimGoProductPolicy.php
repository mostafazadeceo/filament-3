<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;

class EsimGoProductPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('esim_go.product.view');
    }

    public function view(User $user, EsimGoProduct $record): bool
    {
        return IamAuthorization::allows('esim_go.product.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('esim_go.product.manage');
    }

    public function update(User $user, EsimGoProduct $record): bool
    {
        return IamAuthorization::allows('esim_go.product.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, EsimGoProduct $record): bool
    {
        return IamAuthorization::allows('esim_go.product.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
