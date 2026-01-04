<?php

namespace Haida\FilamentCommerceExperience\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;

class ExperienceCsatSurveyPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['experience.csat.view', 'experience.csat.manage'], null, $user);
    }

    public function view(User $user, ExperienceCsatSurvey $record): bool
    {
        return IamAuthorization::allowsAny(['experience.csat.view', 'experience.csat.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('experience.csat.manage', null, $user);
    }

    public function update(User $user, ExperienceCsatSurvey $record): bool
    {
        return IamAuthorization::allows('experience.csat.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, ExperienceCsatSurvey $record): bool
    {
        return IamAuthorization::allows('experience.csat.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
