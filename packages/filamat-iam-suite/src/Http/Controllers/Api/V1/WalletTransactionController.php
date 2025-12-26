<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionController extends BaseController
{
    protected function modelClass(): string
    {
        return WalletTransaction::class;
    }

    protected function validationRules(string $action): array
    {
        return [
            'wallet_id' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'status' => ['nullable', 'string'],
            'idempotency_key' => ['nullable', 'string'],
        ];
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

        return $query->whereHas('wallet', function (Builder $builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });
    }
}
