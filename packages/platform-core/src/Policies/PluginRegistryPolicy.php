<?php

namespace Haida\PlatformCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\PlatformCore\Models\PluginRegistry;
use Illuminate\Contracts\Auth\Authenticatable;

class PluginRegistryPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('platform.plugins.view', null, $user);
    }

    public function view(?Authenticatable $user, PluginRegistry $record): bool
    {
        return IamAuthorization::allows('platform.plugins.view', null, $user);
    }

    public function create(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }

    public function update(?Authenticatable $user, PluginRegistry $record): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }

    public function delete(?Authenticatable $user, PluginRegistry $record): bool
    {
        return IamAuthorization::allows('platform.plugins.manage', null, $user);
    }
}
