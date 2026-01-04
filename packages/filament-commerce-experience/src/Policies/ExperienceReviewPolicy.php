<?php

namespace Haida\FilamentCommerceExperience\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceExperience\Models\ExperienceReview;

class ExperienceReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['experience.reviews.view', 'experience.reviews.moderate'], null, $user);
    }

    public function view(User $user, ExperienceReview $record): bool
    {
        return IamAuthorization::allowsAny(['experience.reviews.view', 'experience.reviews.moderate'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate', null, $user);
    }

    public function update(User $user, ExperienceReview $record): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, ExperienceReview $record): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
