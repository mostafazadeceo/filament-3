<?php

namespace Haida\FilamentCryptoCore\Models\Concerns;

use Filamat\IamSuite\Support\BelongsToTenant;
use Filamat\IamSuite\Support\TenantContext;

trait UsesTenant
{
    use BelongsToTenant;

    protected static function bootUsesTenant(): void
    {
        static::creating(function ($model) {
            if (! $model->tenant_id) {
                $model->tenant_id = TenantContext::getTenantId();
            }
        });
    }
}
