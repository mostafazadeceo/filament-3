<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class BasePettyCashRequest extends FormRequest
{
    protected function tenantId(): ?int
    {
        return TenantContext::getTenantId();
    }
}
