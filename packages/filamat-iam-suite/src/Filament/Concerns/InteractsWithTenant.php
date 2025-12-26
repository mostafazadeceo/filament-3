<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Concerns;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithTenant
{
    protected static function tenantSelect(string $name = 'tenant_id', bool $required = true): Select
    {
        return Select::make($name)
            ->label('فضای کاری')
            ->options(fn () => Tenant::query()->pluck('name', 'id')->toArray())
            ->searchable()
            ->required($required)
            ->default(fn () => TenantContext::getTenantId())
            ->visible(fn () => TenantContext::shouldBypass())
            ->disabled(fn () => ! TenantContext::shouldBypass())
            ->dehydrateStateUsing(fn ($state) => TenantContext::shouldBypass() ? $state : TenantContext::getTenantId())
            ->dehydrated(true);
    }

    protected static function scopeByTenant(Builder $query, string $column = 'tenant_id'): Builder
    {
        if (TenantContext::shouldBypass()) {
            return $query;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $query;
        }

        return $query->where($column, $tenantId);
    }
}
