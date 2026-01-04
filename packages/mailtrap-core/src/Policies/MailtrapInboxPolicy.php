<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapInbox;

class MailtrapInboxPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.inbox.view');
    }

    public function view(User $user, MailtrapInbox $record): bool
    {
        return IamAuthorization::allows('mailtrap.inbox.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.inbox.manage');
    }

    public function update(User $user, MailtrapInbox $record): bool
    {
        return IamAuthorization::allows('mailtrap.inbox.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapInbox $record): bool
    {
        return IamAuthorization::allows('mailtrap.inbox.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
