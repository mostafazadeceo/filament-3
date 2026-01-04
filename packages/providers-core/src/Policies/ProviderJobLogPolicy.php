<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Illuminate\Contracts\Auth\Authenticatable;

class ProviderJobLogPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('provider.job_log.view', null, $user);
    }

    public function view(?Authenticatable $user, ProviderJobLog $log): bool
    {
        return IamAuthorization::allows('provider.job_log.view', IamAuthorization::resolveTenantFromRecord($log), $user);
    }

    public function reprocess(?Authenticatable $user, ProviderJobLog $log): bool
    {
        return IamAuthorization::allows('provider.job_log.manage', IamAuthorization::resolveTenantFromRecord($log), $user);
    }
}
