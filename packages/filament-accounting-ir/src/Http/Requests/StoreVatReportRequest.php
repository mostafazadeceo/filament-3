<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\VatReport;

class StoreVatReportRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', VatReport::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'vat_period_id' => [
                'required',
                'integer',
                Rule::exists('accounting_ir_vat_periods', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'sales_base' => ['nullable', 'numeric', 'min:0'],
            'sales_tax' => ['nullable', 'numeric', 'min:0'],
            'purchase_base' => ['nullable', 'numeric', 'min:0'],
            'purchase_tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
