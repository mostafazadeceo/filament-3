<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;

class UpdatePurchaseInvoiceRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('purchase_invoice');

        return $invoice instanceof PurchaseInvoice
            ? auth()->user()?->can('update', $invoice)
            : false;
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
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_branches', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'fiscal_year_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_fiscal_years', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'party_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_parties', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'invoice_no' => ['sometimes', 'string', 'max:64'],
            'invoice_date' => ['sometimes', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'currency' => ['nullable', 'string', 'max:8'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'lines' => ['nullable', 'array'],
            'lines.*.product_id' => ['nullable', 'integer', Rule::exists('accounting_ir_products_services', 'id')],
            'lines.*.description' => ['nullable', 'string', 'max:255'],
            'lines.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'lines.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'lines.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
            'lines.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
            'lines.*.line_total' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
