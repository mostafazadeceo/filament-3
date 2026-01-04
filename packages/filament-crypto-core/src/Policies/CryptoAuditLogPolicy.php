<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoAuditEvent;

class CryptoAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.view', 'crypto.audit.manage', 'crypto.manage'], null, $user);
    }

    public function view(User $user, CryptoAuditEvent $record): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.view', 'crypto.audit.manage', 'crypto.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.manage', 'crypto.manage'], null, $user);
    }

    public function update(User $user, CryptoAuditEvent $record): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.manage', 'crypto.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoAuditEvent $record): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.manage', 'crypto.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoAuditEvent $record): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.manage', 'crypto.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoAuditEvent $record): bool
    {
        return IamAuthorization::allowsAny(['crypto.audit.manage', 'crypto.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
