<?php

namespace Haida\FilamentAiCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentAiCore\Models\AiPolicy;
use Illuminate\Contracts\Auth\Authenticatable;

class AiPolicyPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return IamAuthorization::allows('ai.manage', null, $user);
    }

    public function view(?Authenticatable $user, AiPolicy $policy): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($policy), $user);
    }

    public function create(?Authenticatable $user): bool
    {
        return IamAuthorization::allows('ai.manage', null, $user);
    }

    public function update(?Authenticatable $user, AiPolicy $policy): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($policy), $user);
    }

    public function delete(?Authenticatable $user, AiPolicy $policy): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($policy), $user);
    }
}
