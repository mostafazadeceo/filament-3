<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class UpdateJournalEntryRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $entry = $this->route('journal_entry');

        return $entry instanceof JournalEntry
            ? auth()->user()?->can('update', $entry)
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
            'fiscal_period_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_fiscal_periods', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'entry_no' => ['sometimes', 'string', 'max:64'],
            'entry_date' => ['sometimes', 'date'],
            'status' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'lines' => ['nullable', 'array'],
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
