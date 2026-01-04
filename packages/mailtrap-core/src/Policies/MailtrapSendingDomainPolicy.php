<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;

class MailtrapSendingDomainPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.domain.view');
    }

    public function view(User $user, MailtrapSendingDomain $record): bool
    {
        return IamAuthorization::allows('mailtrap.domain.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.domain.manage');
    }

    public function update(User $user, MailtrapSendingDomain $record): bool
    {
        return IamAuthorization::allows('mailtrap.domain.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapSendingDomain $record): bool
    {
        return IamAuthorization::allows('mailtrap.domain.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
