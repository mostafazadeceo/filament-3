<?php

namespace Haida\CommerceCatalog\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\CommerceCatalog\Models\CatalogProduct;

class CatalogProductPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'catalog.product.view',
            'catalog.product.manage',
        ], null, $user);
    }

    public function view(User $user, CatalogProduct $product): bool
    {
        return IamAuthorization::allowsAny([
            'catalog.product.view',
            'catalog.product.manage',
        ], IamAuthorization::resolveTenantFromRecord($product), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('catalog.product.manage', null, $user);
    }

    public function update(User $user, CatalogProduct $product): bool
    {
        return IamAuthorization::allows('catalog.product.manage', IamAuthorization::resolveTenantFromRecord($product), $user);
    }

    public function delete(User $user, CatalogProduct $product): bool
    {
        return IamAuthorization::allows('catalog.product.manage', IamAuthorization::resolveTenantFromRecord($product), $user);
    }

    public function restore(User $user, CatalogProduct $product): bool
    {
        return IamAuthorization::allows('catalog.product.manage', IamAuthorization::resolveTenantFromRecord($product), $user);
    }

    public function forceDelete(User $user, CatalogProduct $product): bool
    {
        return IamAuthorization::allows('catalog.product.manage', IamAuthorization::resolveTenantFromRecord($product), $user);
    }
}
