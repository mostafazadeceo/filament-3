<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\MailtrapCore\Models\MailtrapCampaign;

class MailtrapCampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.view');
    }

    public function view(User $user, MailtrapCampaign $record): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.manage');
    }

    public function update(User $user, MailtrapCampaign $record): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function delete(User $user, MailtrapCampaign $record): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function send(User $user, MailtrapCampaign $record): bool
    {
        return IamAuthorization::allows('mailtrap.campaign.send', IamAuthorization::resolveTenantFromRecord($record));
    }
}
