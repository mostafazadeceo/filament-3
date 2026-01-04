<?php

namespace Haida\FilamentAiCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentAiCore\Models\AiRequest;
use Illuminate\Contracts\Auth\Authenticatable;

class AiRequestPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return IamAuthorization::allows('ai.audit.view', null, $user);
    }

    public function view(?Authenticatable $user, AiRequest $request): bool
    {
        return IamAuthorization::allows('ai.audit.view', IamAuthorization::resolveTenantFromRecord($request), $user);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}
