<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\RequiresReason;
use Filamat\IamSuite\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends BaseController
{
    use RequiresReason;

    protected function modelClass(): string
    {
        return Permission::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'tenant_id' => ['nullable', 'integer'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function store(Request $request, ?int $parentId = null): Response
    {
        $data = $request->validate($this->validationRules('store'));
        $reason = $this->resolveReason($request, $data);
        if ($response = $this->ensureReason($request, $reason)) {
            return $response;
        }

        unset($data['reason']);
        $data = $this->mutateDataForTenant($data, 'store');

        $model = $this->modelClass();
        $item = $model::query()->create($data);

        app(AuditService::class)->log('permission.created', $item, ['reason' => $reason], $request->user());

        return response(['data' => $item], 201);
    }

    public function update(Request $request, int $id): Response
    {
        $data = $request->validate($this->validationRules('update'));
        $reason = $this->resolveReason($request, $data);
        if ($response = $this->ensureReason($request, $reason)) {
            return $response;
        }

        unset($data['reason']);
        $data = $this->mutateDataForTenant($data, 'update');

        $item = $this->query()->findOrFail($id);
        $item->update($data);

        app(AuditService::class)->log('permission.updated', $item, ['reason' => $reason], $request->user());

        return response(['data' => $item], 200);
    }

    public function destroy(int $id): Response
    {
        $request = request();
        $reason = $this->resolveReason($request);
        if ($response = $this->ensureReason($request, $reason)) {
            return $response;
        }

        $item = $this->query()->findOrFail($id);
        $item->delete();

        app(AuditService::class)->log('permission.deleted', $item, ['reason' => $reason], $request->user());

        return response(['message' => 'حذف شد.'], 200);
    }
}
