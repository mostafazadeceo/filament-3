<?php

namespace Haida\CommerceCatalog\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceCatalog\Models\CatalogCollection;

class CatalogCollectionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('catalog.collection.manage', null, $user);
    }

    public function view(User $user, CatalogCollection $collection): bool
    {
        return IamAuthorization::allows('catalog.collection.manage', IamAuthorization::resolveTenantFromRecord($collection), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('catalog.collection.manage', null, $user);
    }

    public function update(User $user, CatalogCollection $collection): bool
    {
        return IamAuthorization::allows('catalog.collection.manage', IamAuthorization::resolveTenantFromRecord($collection), $user);
    }

    public function delete(User $user, CatalogCollection $collection): bool
    {
        return IamAuthorization::allows('catalog.collection.manage', IamAuthorization::resolveTenantFromRecord($collection), $user);
    }
}
