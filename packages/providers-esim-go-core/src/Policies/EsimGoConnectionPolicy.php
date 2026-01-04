<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;

class EsimGoConnectionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('esim_go.connection.view');
    }

    public function view(User $user, EsimGoConnection $record): bool
    {
        return IamAuthorization::allows('esim_go.connection.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('esim_go.connection.manage');
    }

    public function update(User $user, EsimGoConnection $record): bool
    {
        return IamAuthorization::allows('esim_go.connection.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, EsimGoConnection $record): bool
    {
        return IamAuthorization::allows('esim_go.connection.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
