<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class UpdateAccountingCompanyRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company instanceof AccountingCompany
            ? auth()->user()?->can('update', $company)
            : false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
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
