<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\DelegatedAdminScope;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;

class DelegatedAdminService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function hasScope(Authenticatable $user, Tenant $tenant, string $resource, string $action, array $context = []): bool
    {
        if (! (bool) config('filamat-iam.features.delegated_admin', true)) {
            return false;
        }

        $scopes = DelegatedAdminScope::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('user_id', $user->getAuthIdentifier())
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        foreach ($scopes as $scope) {
            if (! $this->matchesScope($scope->resource_scopes ?? [], $resource)) {
                continue;
            }

            if (! $this->matchesScope($scope->action_scopes ?? [], $action)) {
                continue;
            }

            if (! $this->matchesDataScope($scope->data_scopes ?? [], $context)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param  array<int, string>  $scopes
     */
    protected function matchesScope(array $scopes, string $value): bool
    {
        if ($scopes === []) {
            return true;
        }

        if (in_array('*', $scopes, true)) {
            return true;
        }

        return in_array($value, $scopes, true);
    }

    /**
     * @param  array<string, mixed>  $dataScopes
     * @param  array<string, mixed>  $context
     */
    protected function matchesDataScope(array $dataScopes, array $context): bool
    {
        if ($dataScopes === []) {
            return true;
        }

        if (! empty($dataScopes['all'])) {
            return true;
        }

        if (isset($context['user_id']) && isset($dataScopes['user_ids']) && is_array($dataScopes['user_ids'])) {
            return in_array($context['user_id'], $dataScopes['user_ids'], true);
        }

        if (isset($context['group_id']) && isset($dataScopes['group_ids']) && is_array($dataScopes['group_ids'])) {
            return in_array($context['group_id'], $dataScopes['group_ids'], true);
        }

        return false;
    }
}
