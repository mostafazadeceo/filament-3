<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Vendor\FilamentAccountingIr\Models\PayrollTable;

class StorePayrollTableRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', PayrollTable::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'table_type' => ['required', 'string', 'max:64'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'payload' => ['required', 'array'],
        ];
    }
}
