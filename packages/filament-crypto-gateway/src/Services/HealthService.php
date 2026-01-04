<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Illuminate\Support\Arr;

class HealthService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function providers(?int $tenantId = null): array
    {
        $query = CryptoProviderAccount::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get()->map(fn (CryptoProviderAccount $account) => [
            'provider' => $account->provider,
            'env' => $account->env?->value ?? (string) $account->env,
            'active' => (bool) $account->is_active,
            'meta' => Arr::only($account->config_json ?? [], ['health', 'mode']),
        ])->all();
    }
}
