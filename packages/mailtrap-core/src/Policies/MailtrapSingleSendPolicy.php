<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapSingleSend;

class MailtrapSingleSendPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.send.single');
    }

    public function view(User $user, MailtrapSingleSend $record): bool
    {
        return IamAuthorization::allows('mailtrap.send.single', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.send.single');
    }
}
