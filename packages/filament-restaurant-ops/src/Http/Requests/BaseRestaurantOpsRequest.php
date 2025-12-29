<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRestaurantOpsRequest extends FormRequest
{
    protected function tenantId(): ?int
    {
        return TenantContext::getTenantId();
    }
}
