<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentMailOps\Models\MailOutboundMessage;

class MailOutboundMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailops.outbound.view');
    }

    public function view(User $user, MailOutboundMessage $record): bool
    {
        return IamAuthorization::allows('mailops.outbound.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailops.outbound.send');
    }

    public function update(User $user, MailOutboundMessage $record): bool
    {
        return false;
    }

    public function delete(User $user, MailOutboundMessage $record): bool
    {
        return false;
    }
}
