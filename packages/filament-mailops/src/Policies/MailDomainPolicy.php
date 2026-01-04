<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMailOps\Models\MailDomain;

class MailDomainPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailops.domain.view');
    }

    public function view(User $user, MailDomain $record): bool
    {
        return IamAuthorization::allows('mailops.domain.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailops.domain.manage');
    }

    public function update(User $user, MailDomain $record): bool
    {
        return IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailDomain $record): bool
    {
        return IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
