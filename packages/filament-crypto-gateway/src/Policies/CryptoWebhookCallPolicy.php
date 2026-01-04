<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;

class CryptoWebhookCallPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.webhooks.view',
            'crypto.webhooks.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoWebhookCall $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.webhooks.view',
            'crypto.webhooks.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.webhooks.manage', null, $user);
    }

    public function update(User $user, CryptoWebhookCall $record): bool
    {
        return IamAuthorization::allows('crypto.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoWebhookCall $record): bool
    {
        return IamAuthorization::allows('crypto.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoWebhookCall $record): bool
    {
        return IamAuthorization::allows('crypto.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoWebhookCall $record): bool
    {
        return IamAuthorization::allows('crypto.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
