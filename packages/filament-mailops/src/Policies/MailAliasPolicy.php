<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMailOps\Models\MailAlias;

class MailAliasPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailops.alias.view');
    }

    public function view(User $user, MailAlias $record): bool
    {
        return IamAuthorization::allows('mailops.alias.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailops.alias.manage');
    }

    public function update(User $user, MailAlias $record): bool
    {
        return IamAuthorization::allows('mailops.alias.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailAlias $record): bool
    {
        return IamAuthorization::allows('mailops.alias.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
