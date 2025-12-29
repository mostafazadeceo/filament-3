<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Watcher;

class WatcherPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.watcher.view',
            'workhub.watcher.manage',
        ], null, $user);
    }

    public function view(User $user, Watcher $watcher): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.watcher.view',
            'workhub.watcher.manage',
        ], IamAuthorization::resolveTenantFromRecord($watcher), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.watcher.manage', null, $user);
    }

    public function delete(User $user, Watcher $watcher): bool
    {
        return IamAuthorization::allows('workhub.watcher.manage', IamAuthorization::resolveTenantFromRecord($watcher), $user);
    }
}
