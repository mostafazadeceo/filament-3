<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEInvoiceRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('e_invoice')) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'company_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_companies', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'sales_invoice_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_sales_invoices', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'provider_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_e_invoice_providers', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'invoice_type' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:32'],
            'unique_tax_id' => ['nullable', 'string', 'max:255'],
            'payload_version' => ['nullable', 'string', 'max:32'],
            'issued_at' => ['nullable', 'date'],
            'payload' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
