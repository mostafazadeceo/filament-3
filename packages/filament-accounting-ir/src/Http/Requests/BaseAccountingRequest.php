<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseAccountingRequest extends FormRequest
{
    protected function tenantId(): ?int
    {
        return TenantContext::getTenantId();
    }
}
