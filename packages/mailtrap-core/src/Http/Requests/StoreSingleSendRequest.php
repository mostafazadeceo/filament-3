<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;

class StoreSingleSendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'connection_id' => ['required', 'exists:mailtrap_connections,id'],
            'to_email' => ['required', 'email', 'max:190'],
            'to_name' => ['nullable', 'string', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'text_body' => ['nullable', 'string'],
            'html_body' => ['nullable', 'string'],
            'from_email' => ['nullable', 'email', 'max:190'],
            'from_name' => ['nullable', 'string', 'max:190'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('tenant_id')) {
            $this->merge([
                'tenant_id' => TenantContext::getTenantId(),
            ]);
        }
    }
}
