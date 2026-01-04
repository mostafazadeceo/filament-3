<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapMessage;

class MailtrapMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.message.view');
    }

    public function view(User $user, MailtrapMessage $record): bool
    {
        return IamAuthorization::allows('mailtrap.message.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.message.view');
    }

    public function update(User $user, MailtrapMessage $record): bool
    {
        return IamAuthorization::allows('mailtrap.message.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapMessage $record): bool
    {
        return IamAuthorization::allows('mailtrap.message.view', IamAuthorization::resolveTenantFromRecord($record));
    }
}
