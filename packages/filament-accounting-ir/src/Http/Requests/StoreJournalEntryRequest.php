<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class StoreJournalEntryRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', JournalEntry::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => [
                'required',
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
                'required',
                'integer',
                Rule::exists('accounting_ir_fiscal_years', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'fiscal_period_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_fiscal_periods', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'entry_no' => ['required', 'string', 'max:64'],
            'entry_date' => ['required', 'date'],
            'status' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'lines' => ['nullable', 'array', 'min:1'],
            'lines.*.account_id' => [
                'required_with:lines',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'lines.*.description' => ['nullable', 'string', 'max:255'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.currency' => ['nullable', 'string', 'max:8'],
            'lines.*.amount' => ['nullable', 'numeric', 'min:0'],
            'lines.*.exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'lines.*.dimensions' => ['nullable', 'array'],
        ];
    }
}
