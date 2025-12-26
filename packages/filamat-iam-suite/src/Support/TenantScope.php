<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (TenantContext::shouldBypass()) {
            return;
        }

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return;
        }

        $column = $model->getTable().'.tenant_id';
        $query = $builder->getQuery();

        if ($this->shouldShareByOrganization($model, $tenant)) {
            $tenantIds = $tenant->organization?->tenants()->pluck('id')->all() ?? [$tenant->getKey()];
            $query->whereIn($column, $tenantIds);

            return;
        }

        $query->where($column, $tenant->getKey());
    }

    protected function shouldShareByOrganization(Model $model, Tenant $tenant): bool
    {
        $organization = $tenant->organization;
        if (! $organization || $organization->shared_data_mode !== 'shared_by_organization') {
            return false;
        }

        $sharedModels = (array) config('filamat-iam.shared_models', []);

        return in_array($model::class, $sharedModels, true);
    }
}
