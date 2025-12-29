<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\Party;

class UpdatePartyRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $party = $this->route('party');

        return $party instanceof Party
            ? auth()->user()?->can('update', $party)
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
            'party_type' => ['sometimes', 'string', 'max:32'],
            'name' => ['sometimes', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:32'],
            'economic_code' => ['nullable', 'string', 'max:32'],
            'registration_number' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
