<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWatcherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        if (! $tenantId && $this->route('workItem')) {
            $tenantId = $this->route('workItem')->tenant_id;
        }

        $userRule = $tenantId
            ? Rule::exists('tenant_user', 'user_id')->where('tenant_id', $tenantId)
            : Rule::exists('users', 'id');

        return [
            'user_id' => ['required', 'integer', $userRule],
        ];
    }
}
