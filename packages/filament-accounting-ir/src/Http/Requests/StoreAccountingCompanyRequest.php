<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class StoreAccountingCompanyRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', AccountingCompany::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:32'],
            'economic_code' => ['nullable', 'string', 'max:32'],
            'registration_number' => ['nullable', 'string', 'max:32'],
            'vat_number' => ['nullable', 'string', 'max:64'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'base_currency' => ['nullable', 'string', 'max:8'],
            'is_active' => ['boolean'],
        ];
    }
}
