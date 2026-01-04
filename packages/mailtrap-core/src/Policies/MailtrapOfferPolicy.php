<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapOffer;

class MailtrapOfferPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.offer.view');
    }

    public function view(User $user, MailtrapOffer $record): bool
    {
        return IamAuthorization::allows('mailtrap.offer.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.offer.manage');
    }

    public function update(User $user, MailtrapOffer $record): bool
    {
        return IamAuthorization::allows('mailtrap.offer.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapOffer $record): bool
    {
        return IamAuthorization::allows('mailtrap.offer.manage', IamAuthorization::resolveTenantFromRecord($record));
    }
}
