<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

class UpdatePayrollTableRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('payroll_table')) ?? false;
    }

    public function rules(): array
    {
        return [
            'table_type' => ['sometimes', 'string', 'max:64'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'payload' => ['sometimes', 'array'],
        ];
    }
}
