<?php

namespace Haida\PaymentsOrchestrator\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;

class PaymentIntentPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.payment.view',
            'commerce.payment.manage',
        ], null, $user);
    }

    public function view(User $user, PaymentIntent $intent): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.payment.view',
            'commerce.payment.manage',
        ], IamAuthorization::resolveTenantFromRecord($intent), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.payment.manage', null, $user);
    }

    public function update(User $user, PaymentIntent $intent): bool
    {
        return IamAuthorization::allows('commerce.payment.manage', IamAuthorization::resolveTenantFromRecord($intent), $user);
    }

    public function delete(User $user, PaymentIntent $intent): bool
    {
        return IamAuthorization::allows('commerce.payment.manage', IamAuthorization::resolveTenantFromRecord($intent), $user);
    }
}
