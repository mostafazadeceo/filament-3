<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filamat\IamSuite\Support\TenantContext;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentChat\Models\ChatUserLink;
use Illuminate\Support\Arr;

class ChatUserLinkPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('chat.user.view');
    }

    public function view(User $user, ChatUserLink $record): bool
    {
        return IamAuthorization::allows('chat.user.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public function create(User $user): bool
    {
        $tenant = TenantContext::getTenant();

        return $this->canManage($user, $tenant);
    }

    public function update(User $user, ChatUserLink $record): bool
    {
        $tenant = IamAuthorization::resolveTenantFromRecord($record);

        return $this->canManage($user, $tenant);
    }

    public function delete(User $user, ChatUserLink $record): bool
    {
        $tenant = IamAuthorization::resolveTenantFromRecord($record);

        return $this->canManage($user, $tenant);
    }

    protected function canManage(User $user, ?Tenant $tenant): bool
    {
        if (MegaSuperAdmin::check($user)) {
            return true;
        }

        if (! IamAuthorization::allows('chat.user.manage', $tenant, $user)) {
            return false;
        }

        return $this->ownerManageEnabled($tenant);
    }

    protected function ownerManageEnabled(?Tenant $tenant): bool
    {
        if (! $tenant) {
            return true;
        }

        $featureFlag = (string) config('filamat-iam.chat.owner_manage_flag', 'tenant_owner_manage');
        $flags = Arr::wrap(data_get($tenant->organization?->settings, 'entitlements.feature_flags.chat', []));
        $flags = array_values(array_filter(array_map('strval', $flags)));

        if ($flags === []) {
            return true;
        }

        return in_array($featureFlag, $flags, true);
    }
}
