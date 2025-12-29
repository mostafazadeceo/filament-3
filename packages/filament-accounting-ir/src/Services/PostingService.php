<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;

class PostingService
{
    /**
     * @var array<int, AccountingCompanySetting|null>
     */
    protected array $settingsCache = [];

    public function postSalesInvoice(SalesInvoice $invoice): ?JournalEntry
    {
        if ($this->hasEntry('sales_invoice', $invoice->getKey())) {
            return $this->findEntry('sales_invoice', $invoice->getKey());
        }

        $invoice->loadMissing('lines');

        $accountsReceivable = $this->resolveAccountId('accounts_receivable', $invoice->company_id);
        $salesRevenue = $this->resolveAccountId('sales_revenue', $invoice->company_id);
        $salesTax = $invoice->tax_total > 0
            ? $this->resolveAccountId('sales_tax', $invoice->company_id)
            : null;

        $lines = [
            [
                'account_id' => $accountsReceivable,
                'description' => 'ثبت حساب دریافتنی',
                'debit' => $invoice->total,
                'credit' => 0,
            ],
            [
                'account_id' => $salesRevenue,
                'description' => 'ثبت فروش',
                'debit' => 0,
                'credit' => $invoice->subtotal,
            ],
        ];

        if ($salesTax) {
            $lines[] = [
                'account_id' => $salesTax,
                'description' => 'مالیات و عوارض فروش',
                'debit' => 0,
                'credit' => $invoice->tax_total,
            ];
        }

        return $this->createEntry(
            $invoice->company_id,
            $invoice->tenant_id,
            $invoice->branch_id,
            $invoice->fiscal_year_id,
            $invoice->invoice_date?->toDateString(),
            $this->resolveEntryNo('SI', $invoice->invoice_no, $invoice->getKey()),
            'sales_invoice',
            $invoice->getKey(),
            'سند اتومات فروش',
            $lines
        );
    }

    public function postPurchaseInvoice(PurchaseInvoice $invoice): ?JournalEntry
    {
        if ($this->hasEntry('purchase_invoice', $invoice->getKey())) {
            return $this->findEntry('purchase_invoice', $invoice->getKey());
        }

        $invoice->loadMissing('lines');

        $accountsPayable = $this->resolveAccountId('accounts_payable', $invoice->company_id);
        $purchaseExpense = $this->resolveAccountId('purchase_expense', $invoice->company_id);
        $purchaseTax = $invoice->tax_total > 0
            ? $this->resolveAccountId('purchase_tax', $invoice->company_id)
            : null;

        $lines = [
            [
                'account_id' => $purchaseExpense,
                'description' => 'ثبت خرید',
                'debit' => $invoice->subtotal,
                'credit' => 0,
            ],
        ];

        if ($purchaseTax) {
            $lines[] = [
                'account_id' => $purchaseTax,
                'description' => 'مالیات و عوارض خرید',
                'debit' => $invoice->tax_total,
                'credit' => 0,
            ];
        }

        $lines[] = [
            'account_id' => $accountsPayable,
            'description' => 'ثبت حساب پرداختنی',
            'debit' => 0,
            'credit' => $invoice->total,
        ];

        return $this->createEntry(
            $invoice->company_id,
            $invoice->tenant_id,
            $invoice->branch_id,
            $invoice->fiscal_year_id,
            $invoice->invoice_date?->toDateString(),
            $this->resolveEntryNo('PI', $invoice->invoice_no, $invoice->getKey()),
            'purchase_invoice',
            $invoice->getKey(),
            'سند اتومات خرید',
            $lines
        );
    }

    protected function createEntry(
        int $companyId,
        ?int $tenantId,
        ?int $branchId,
        int $fiscalYearId,
        ?string $entryDate,
        string $entryNo,
        string $sourceType,
        int $sourceId,
        string $description,
        array $lines
    ): JournalEntry {
        $entryDate ??= now()->toDateString();
        $fiscalPeriodId = $this->resolveFiscalPeriodId($companyId, $fiscalYearId, $entryDate);

        $totals = $this->sumTotals($lines);
        $requiresApproval = $this->resolvePostingRequiresApproval($companyId);

        return DB::transaction(function () use (
            $companyId,
            $tenantId,
            $branchId,
            $fiscalYearId,
            $fiscalPeriodId,
            $entryDate,
            $entryNo,
            $sourceType,
            $sourceId,
            $description,
            $lines,
            $totals,
            $requiresApproval
        ): JournalEntry {
            $entry = JournalEntry::query()->create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'fiscal_year_id' => $fiscalYearId,
                'fiscal_period_id' => $fiscalPeriodId,
                'entry_no' => $entryNo,
                'entry_date' => $entryDate,
                'status' => 'draft',
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'total_debit' => $totals['debit'],
                'total_credit' => $totals['credit'],
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create([
                    'tenant_id' => $tenantId,
                    'company_id' => $companyId,
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'currency' => $line['currency'] ?? 'IRR',
                ]);
            }

            if (! $requiresApproval) {
                $entry = app(JournalEntryService::class)->post($entry);
            }

            return $entry->refresh();
        });
    }

    protected function resolveAccountId(string $configKey, int $companyId): int
    {
        $accountId = $this->resolvePostingAccounts($companyId)[$configKey] ?? null;
        if (! $accountId) {
            throw ValidationException::withMessages([
                'accounts' => "حساب پیش‌فرض {$configKey} تنظیم نشده است.",
            ]);
        }

        $account = ChartAccount::query()->find($accountId);
        if (! $account || (int) $account->company_id !== $companyId) {
            throw ValidationException::withMessages([
                'accounts' => "حساب پیش‌فرض {$configKey} معتبر نیست.",
            ]);
        }

        if (! $account->is_postable) {
            throw ValidationException::withMessages([
                'accounts' => "حساب پیش‌فرض {$configKey} قابل ثبت نیست.",
            ]);
        }

        return (int) $account->getKey();
    }

    /**
     * @return array<string, int|null>
     */
    protected function resolvePostingAccounts(int $companyId): array
    {
        $accounts = (array) config('filament-accounting-ir.ledger.posting_accounts', []);
        $settings = $this->resolveCompanySetting($companyId);

        if ($settings?->posting_accounts) {
            $accounts = array_merge($accounts, $settings->posting_accounts);
        }

        return $accounts;
    }

    protected function resolvePostingRequiresApproval(int $companyId): bool
    {
        $settings = $this->resolveCompanySetting($companyId);

        return $settings
            ? (bool) $settings->posting_requires_approval
            : (bool) config('filament-accounting-ir.ledger.posting_requires_approval', true);
    }

    protected function resolveCompanySetting(int $companyId): ?AccountingCompanySetting
    {
        if (array_key_exists($companyId, $this->settingsCache)) {
            return $this->settingsCache[$companyId];
        }

        return $this->settingsCache[$companyId] = AccountingCompanySetting::query()
            ->where('company_id', $companyId)
            ->first();
    }

    protected function resolveFiscalPeriodId(int $companyId, int $fiscalYearId, string $entryDate): ?int
    {
        return FiscalPeriod::query()
            ->where('company_id', $companyId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->where('start_date', '<=', $entryDate)
            ->where('end_date', '>=', $entryDate)
            ->value('id');
    }

    protected function resolveEntryNo(string $prefix, ?string $sourceNo, int $sourceId): string
    {
        $suffix = $sourceNo && $sourceNo !== '' ? $sourceNo : (string) $sourceId;

        return sprintf('%s-%s', $prefix, $suffix);
    }

    protected function hasEntry(string $sourceType, int $sourceId): bool
    {
        return JournalEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->exists();
    }

    protected function findEntry(string $sourceType, int $sourceId): ?JournalEntry
    {
        return JournalEntry::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();
    }

    protected function sumTotals(array $lines): array
    {
        $debit = 0.0;
        $credit = 0.0;

        foreach ($lines as $line) {
            $debit += (float) ($line['debit'] ?? 0);
            $credit += (float) ($line['credit'] ?? 0);
        }

        return [
            'debit' => $debit,
            'credit' => $credit,
        ];
    }
}
