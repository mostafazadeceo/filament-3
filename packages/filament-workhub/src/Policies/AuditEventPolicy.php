<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\AuditEvent;

class AuditEventPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('workhub.audit.view', null, $user);
    }

    public function view(User $user, AuditEvent $auditEvent): bool
    {
        return IamAuthorization::allows('workhub.audit.view', IamAuthorization::resolveTenantFromRecord($auditEvent), $user);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}
