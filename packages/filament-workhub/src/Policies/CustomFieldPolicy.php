<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\CustomField;

class CustomFieldPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.custom_field.view',
            'workhub.custom_field.manage',
        ], null, $user);
    }

    public function view(User $user, CustomField $field): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.custom_field.view',
            'workhub.custom_field.manage',
        ], IamAuthorization::resolveTenantFromRecord($field), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.custom_field.manage', null, $user);
    }

    public function update(User $user, CustomField $field): bool
    {
        return IamAuthorization::allows('workhub.custom_field.manage', IamAuthorization::resolveTenantFromRecord($field), $user);
    }

    public function delete(User $user, CustomField $field): bool
    {
        return IamAuthorization::allows('workhub.custom_field.manage', IamAuthorization::resolveTenantFromRecord($field), $user);
    }
}
