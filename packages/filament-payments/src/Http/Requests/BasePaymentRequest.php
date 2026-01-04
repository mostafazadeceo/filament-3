<?php

namespace Haida\FilamentPayments\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class BasePaymentRequest extends FormRequest
{
    protected function tenantId(): ?int
    {
        return TenantContext::getTenantId();
    }
}
