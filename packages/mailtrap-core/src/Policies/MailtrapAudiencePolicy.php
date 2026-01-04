<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapAudience;

class MailtrapAudiencePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.audience.view');
    }

    public function view(User $user, MailtrapAudience $record): bool
    {
        return IamAuthorization::allows('mailtrap.audience.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.audience.manage');
    }

    public function update(User $user, MailtrapAudience $record): bool
    {
        return IamAuthorization::allows('mailtrap.audience.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapAudience $record): bool
    {
        return IamAuthorization::allows('mailtrap.audience.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
