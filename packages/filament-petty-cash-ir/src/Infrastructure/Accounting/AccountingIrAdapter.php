<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Accounting;

use Haida\FilamentPettyCashIr\Application\DTO\PostingResult;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;
use Vendor\FilamentAccountingIr\Services\JournalEntryService;

class AccountingIrAdapter implements AccountingAdapterInterface
{
    public function postExpense(PettyCashExpense $expense, PettyCashFund $fund): PostingResult
    {
        $expenseAccountId = $expense->category?->accounting_account_id ?: $fund->default_expense_account_id;
        if (! $expenseAccountId || ! $fund->accounting_cash_account_id) {
            return new PostingResult(null);
        }

        $expenseAccountId = $this->resolveAccountId($expenseAccountId, $expense->company_id, 'هزینه');
        $cashAccountId = $this->resolveAccountId($fund->accounting_cash_account_id, $expense->company_id, 'تنخواه');

        $lines = [
            [
                'account_id' => $expenseAccountId,
                'description' => 'ثبت هزینه تنخواه',
                'debit' => $expense->amount,
                'credit' => 0,
            ],
            [
                'account_id' => $cashAccountId,
                'description' => 'کاهش تنخواه',
                'debit' => 0,
                'credit' => $expense->amount,
            ],
        ];

        $entry = $this->createEntry(
            $expense->company_id,
            $expense->tenant_id,
            $expense->branch_id,
            $expense->expense_date?->toDateString() ?? now()->toDateString(),
            'petty_cash_expense',
            $expense->getKey(),
            'سند تنخواه - پرداخت هزینه',
            $lines
        );

        return new PostingResult($entry);
    }

    public function reverseExpense(PettyCashExpense $expense): PostingResult
    {
        $entry = $this->resolveEntry($expense->accounting_journal_entry_id);
        if (! $entry) {
            return new PostingResult(null);
        }

        $reversalEntry = $this->reverseEntry($entry, 'petty_cash_expense_reverse', $expense->getKey());

        return new PostingResult($reversalEntry);
    }

    public function postReplenishment(PettyCashReplenishment $replenishment, PettyCashFund $fund): PostingResult
    {
        if (! $fund->accounting_cash_account_id || ! $fund->accounting_source_account_id) {
            return new PostingResult(null);
        }

        $cashAccountId = $this->resolveAccountId($fund->accounting_cash_account_id, $replenishment->company_id, 'تنخواه');
        $sourceAccountId = $this->resolveAccountId($fund->accounting_source_account_id, $replenishment->company_id, 'منبع تنخواه');

        $lines = [
            [
                'account_id' => $cashAccountId,
                'description' => 'افزایش تنخواه',
                'debit' => $replenishment->amount,
                'credit' => 0,
            ],
            [
                'account_id' => $sourceAccountId,
                'description' => 'کاهش منبع پرداخت',
                'debit' => 0,
                'credit' => $replenishment->amount,
            ],
        ];

        $entry = $this->createEntry(
            $replenishment->company_id,
            $replenishment->tenant_id,
            $replenishment->branch_id,
            $replenishment->request_date?->toDateString() ?? now()->toDateString(),
            'petty_cash_replenishment',
            $replenishment->getKey(),
            'سند تنخواه - تغذیه',
            $lines
        );

        $treasuryTransactionId = null;
        if ($replenishment->source_treasury_account_id) {
            $treasuryTransaction = TreasuryTransaction::query()->create([
                'tenant_id' => $replenishment->tenant_id,
                'company_id' => $replenishment->company_id,
                'treasury_account_id' => $replenishment->source_treasury_account_id,
                'transaction_type' => 'petty_cash_replenishment',
                'transaction_date' => $replenishment->request_date ?? now()->toDateString(),
                'amount' => $replenishment->amount,
                'currency' => $replenishment->currency ?: 'IRR',
                'reference' => $replenishment->id,
                'description' => 'تغذیه تنخواه',
            ]);

            $treasuryTransactionId = $treasuryTransaction->id;
        }

        return new PostingResult($entry, $treasuryTransactionId);
    }

    public function reverseReplenishment(PettyCashReplenishment $replenishment): PostingResult
    {
        $entry = $this->resolveEntry($replenishment->accounting_journal_entry_id);
        if (! $entry) {
            return new PostingResult(null);
        }

        $reversalEntry = $this->reverseEntry($entry, 'petty_cash_replenishment_reverse', $replenishment->getKey());

        return new PostingResult($reversalEntry);
    }

    public function postSettlement(PettyCashSettlement $settlement): PostingResult
    {
        return new PostingResult(null);
    }

    public function reverseSettlement(PettyCashSettlement $settlement): PostingResult
    {
        return new PostingResult(null);
    }

    protected function createEntry(
        int $companyId,
        ?int $tenantId,
        ?int $branchId,
        string $entryDate,
        string $sourceType,
        int $sourceId,
        string $description,
        array $lines
    ): JournalEntry {
        $fiscalYearId = $this->resolveFiscalYearId($companyId, $entryDate);
        $fiscalPeriodId = $this->resolveFiscalPeriodId($companyId, $fiscalYearId, $entryDate);
        $entryNo = $this->buildEntryNo($sourceType, $sourceId);
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

    protected function reverseEntry(JournalEntry $entry, string $prefix, int $sourceId): JournalEntry
    {
        $entryNo = $this->buildEntryNo($prefix, $sourceId);
        $reversal = app(JournalEntryService::class)->reverse($entry, $entryNo);

        if (! $this->resolvePostingRequiresApproval((int) $entry->company_id)) {
            $reversal = app(JournalEntryService::class)->post($reversal);
        }

        return $reversal->refresh();
    }

    protected function resolveEntry(?int $entryId): ?JournalEntry
    {
        if (! $entryId) {
            return null;
        }

        return JournalEntry::query()->find($entryId);
    }

    protected function buildEntryNo(string $prefix, int $sourceId): string
    {
        $stamp = now()->format('YmdHis');

        return strtoupper(Str::slug($prefix, '-')).'-'.$sourceId.'-'.$stamp;
    }

    protected function sumTotals(array $lines): array
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
        ];
    }

    protected function resolvePostingRequiresApproval(int $companyId): bool
    {
        $setting = AccountingCompanySetting::query()->where('company_id', $companyId)->first();

        return (bool) ($setting?->posting_requires_approval ?? false);
    }

    protected function resolveFiscalYearId(int $companyId, string $entryDate): int
    {
        $year = FiscalYear::query()
            ->where('company_id', $companyId)
            ->whereDate('start_date', '<=', $entryDate)
            ->whereDate('end_date', '>=', $entryDate)
            ->first();

        if (! $year) {
            throw ValidationException::withMessages([
                'fiscal_year_id' => 'سال مالی فعال برای تاریخ ثبت یافت نشد.',
            ]);
        }

        return (int) $year->getKey();
    }

    protected function resolveFiscalPeriodId(int $companyId, int $fiscalYearId, string $entryDate): ?int
    {
        $period = FiscalPeriod::query()
            ->where('company_id', $companyId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->whereDate('start_date', '<=', $entryDate)
            ->whereDate('end_date', '>=', $entryDate)
            ->first();

        return $period?->getKey();
    }

    protected function resolveAccountId(int $accountId, int $companyId, string $label): int
    {
        $account = ChartAccount::query()->find($accountId);
        if (! $account || (int) $account->company_id !== $companyId) {
            throw ValidationException::withMessages([
                'accounts' => "حساب {$label} معتبر نیست.",
            ]);
        }

        if (! $account->is_postable) {
            throw ValidationException::withMessages([
                'accounts' => "حساب {$label} قابل ثبت نیست.",
            ]);
        }

        return (int) $account->getKey();
    }
}
