<?php

namespace Haida\CommerceCheckout\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();

        return [
            'site_id' => [
                'required',
                'integer',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
        ];
    }
}
