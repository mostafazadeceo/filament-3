<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

class UpdateVatReportRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('vat_report')) ?? false;
    }

    public function rules(): array
    {
        return [
            'sales_base' => ['nullable', 'numeric', 'min:0'],
            'sales_tax' => ['nullable', 'numeric', 'min:0'],
            'purchase_base' => ['nullable', 'numeric', 'min:0'],
            'purchase_tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
