<?php

namespace Haida\PlatformCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\PlatformCore\Models\TenantPlugin;
use Illuminate\Contracts\Auth\Authenticatable;

class TenantPluginPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('platform.plugins.view', null, $user);
    }

    public function view(?Authenticatable $user, TenantPlugin $record): bool
    {
        return IamAuthorization::allows('platform.plugins.view', null, $user);
    }

    public function create(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }

    public function update(?Authenticatable $user, TenantPlugin $record): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }

    public function delete(?Authenticatable $user, TenantPlugin $record): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }
}
