<?php

namespace Haida\FilamentCommerceExperience\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseExperienceRequest extends FormRequest
{
    protected function tenantId(): ?int
    {
        return TenantContext::getTenantId();
    }
}
