<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncInboxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $connectionsTable = config('mailtrap-core.tables.connections', 'mailtrap_connections');

        return [
            'connection_id' => [
                'required',
                'integer',
                Rule::exists($connectionsTable, 'id')->where('tenant_id', $tenantId),
            ],
            'force' => ['nullable', 'boolean'],
        ];
    }
}
