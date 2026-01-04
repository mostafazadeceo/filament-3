<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $table = config('mailtrap-core.tables.connections', 'mailtrap_connections');
        /** @var MailtrapConnection|null $connection */
        $connection = $this->route('connection');

        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'name' => [
                'sometimes',
                'string',
                'max:150',
                Rule::unique($table, 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($connection?->getKey()),
            ],
            'api_token' => ['nullable', 'string', 'min:10'],
            'send_api_token' => ['nullable', 'string', 'min:10'],
            'account_id' => ['nullable', 'integer'],
            'default_inbox_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'metadata' => ['nullable', 'array'],
            'test_connection' => ['nullable', 'boolean'],
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
