<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceComplianceDigest;

class CommerceComplianceDigestPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], null, $user);
    }

    public function view(User $user, CommerceComplianceDigest $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.compliance.view',
            'commerce.compliance.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
