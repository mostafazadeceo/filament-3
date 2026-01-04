<?php

namespace Haida\FilamentPayments\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;

class PaymentWebhookEventPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'payments.webhooks.view',
            'payments.webhooks.manage',
        ], null, $user);
    }

    public function view(User $user, PaymentWebhookEvent $record): bool
    {
        return IamAuthorization::allowsAny([
            'payments.webhooks.view',
            'payments.webhooks.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('payments.webhooks.manage', null, $user);
    }

    public function update(User $user, PaymentWebhookEvent $record): bool
    {
        return IamAuthorization::allows('payments.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, PaymentWebhookEvent $record): bool
    {
        return IamAuthorization::allows('payments.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, PaymentWebhookEvent $record): bool
    {
        return IamAuthorization::allows('payments.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, PaymentWebhookEvent $record): bool
    {
        return IamAuthorization::allows('payments.webhooks.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
