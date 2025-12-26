<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\BelongsToTenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

abstract class BaseController
{
    abstract protected function modelClass(): string;

    protected function validationRules(string $action): array
    {
        return [];
    }

    protected function query(): Builder
    {
        $model = $this->modelClass();

        /** @var Builder $query */
        $query = $model::query();

        return $this->applyTenantScope($query);
    }

    protected function applyTenantScope(Builder $query): Builder
    {
        if (TenantContext::shouldBypass()) {
            return $query;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $query;
        }

        $model = $query->getModel();

        if ($model instanceof Tenant) {
            return $query->where($model->getKeyName(), $tenantId);
        }

        if (in_array(BelongsToTenant::class, class_uses_recursive($model), true)) {
            return $query;
        }

        if (Schema::hasColumn($model->getTable(), 'tenant_id')) {
            return $query->where($model->getTable().'.tenant_id', $tenantId);
        }

        if (method_exists($model, 'tenants')) {
            return $query->whereHas('tenants', function (Builder $builder) use ($tenantId) {
                $builder->where('tenants.id', $tenantId);
            });
        }

        return $query;
    }

    protected function mutateDataForTenant(array $data, string $action): array
    {
        if (TenantContext::shouldBypass()) {
            return $data;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $data;
        }

        $model = $this->modelClass();
        $instance = new $model;

        if ($instance instanceof Tenant) {
            return $data;
        }

        if (Schema::hasColumn($instance->getTable(), 'tenant_id')) {
            $data['tenant_id'] = $tenantId;
        }

        return $data;
    }

    public function index(Request $request): Response
    {
        $items = $this->query()->paginate(20);

        return response(['data' => $items], 200);
    }

    public function show(int $id): Response
    {
        $item = $this->query()->findOrFail($id);

        return response(['data' => $item], 200);
    }

    public function store(Request $request, ?int $parentId = null): Response
    {
        $model = $this->modelClass();
        $data = $request->validate($this->validationRules('store'));
        $data = $this->mutateDataForTenant($data, 'store');

        /** @var Model $item */
        $item = $model::query()->create($data);

        return response(['data' => $item], 201);
    }

    public function update(Request $request, int $id): Response
    {
        $model = $this->modelClass();
        $data = $request->validate($this->validationRules('update'));
        $data = $this->mutateDataForTenant($data, 'update');

        $item = $this->query()->findOrFail($id);
        $item->update($data);

        return response(['data' => $item], 200);
    }

    public function destroy(int $id): Response
    {
        $item = $this->query()->findOrFail($id);
        $item->delete();

        return response(['message' => 'حذف شد.'], 200);
    }
}
