<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMailOps\Models\MailMailbox;

class MailMailboxPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailops.mailbox.view');
    }

    public function view(User $user, MailMailbox $record): bool
    {
        return IamAuthorization::allows('mailops.mailbox.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailops.mailbox.manage');
    }

    public function update(User $user, MailMailbox $record): bool
    {
        return IamAuthorization::allows('mailops.mailbox.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailMailbox $record): bool
    {
        return IamAuthorization::allows('mailops.mailbox.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
