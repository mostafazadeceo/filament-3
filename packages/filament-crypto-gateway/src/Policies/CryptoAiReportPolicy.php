<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoAiReport;

class CryptoAiReportPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ai_reports.view',
            'crypto.ai_reports.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoAiReport $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ai_reports.view',
            'crypto.ai_reports.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.ai_reports.manage', null, $user);
    }

    public function update(User $user, CryptoAiReport $record): bool
    {
        return IamAuthorization::allows('crypto.ai_reports.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoAiReport $record): bool
    {
        return IamAuthorization::allows('crypto.ai_reports.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoAiReport $record): bool
    {
        return IamAuthorization::allows('crypto.ai_reports.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoAiReport $record): bool
    {
        return IamAuthorization::allows('crypto.ai_reports.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
