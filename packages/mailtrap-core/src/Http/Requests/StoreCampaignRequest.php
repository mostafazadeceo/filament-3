<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
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
            'audience_id' => ['nullable', 'exists:mailtrap_audiences,id'],
            'name' => ['required', 'string', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'from_email' => ['nullable', 'email', 'max:190'],
            'from_name' => ['nullable', 'string', 'max:190'],
            'html_body' => ['nullable', 'string'],
            'text_body' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'scheduled', 'sending', 'sent', 'failed'])],
            'scheduled_at' => ['nullable', 'date'],
            'settings' => ['nullable', 'array'],
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
