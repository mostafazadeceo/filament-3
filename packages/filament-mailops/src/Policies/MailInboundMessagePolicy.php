<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMailOps\Models\MailInboundMessage;

class MailInboundMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailops.inbound.view');
    }

    public function view(User $user, MailInboundMessage $record): bool
    {
        return IamAuthorization::allows('mailops.inbound.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MailInboundMessage $record): bool
    {
        return false;
    }

    public function delete(User $user, MailInboundMessage $record): bool
    {
        return false;
    }
}
