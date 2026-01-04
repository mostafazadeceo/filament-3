<?php

namespace Haida\FilamentCommerceExperience\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceExperience\Models\ExperienceBuyNowPreference;

class ExperienceBuyNowPreferencePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['experience.buy_now.manage', 'experience.reviews.moderate'], null, $user);
    }

    public function view(User $user, ExperienceBuyNowPreference $record): bool
    {
        return IamAuthorization::allowsAny(['experience.buy_now.manage', 'experience.reviews.moderate'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', null, $user);
    }

    public function update(User $user, ExperienceBuyNowPreference $record): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, ExperienceBuyNowPreference $record): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
