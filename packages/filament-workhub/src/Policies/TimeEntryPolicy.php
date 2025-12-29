<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\TimeEntry;

class TimeEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.time_entry.view',
            'workhub.time_entry.manage',
        ], null, $user);
    }

    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.time_entry.view',
            'workhub.time_entry.manage',
        ], IamAuthorization::resolveTenantFromRecord($timeEntry), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.time_entry.manage', null, $user);
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return IamAuthorization::allows('workhub.time_entry.manage', IamAuthorization::resolveTenantFromRecord($timeEntry), $user);
    }
}
