<?php

namespace Haida\FilamentPettyCashIr\Services;

use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Models\PettyCashAuditEvent;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
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

class PettyCashPostingService
{
    public function submitExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        if ($expense->status !== PettyCashStatuses::EXPENSE_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل ارسال نیست.',
            ]);
        }

        $expense->update([
            'status' => PettyCashStatuses::EXPENSE_SUBMITTED,
            'requested_by' => $expense->requested_by ?: $actorId,
        ]);

        $this->audit($expense, $actorId, 'expense_submitted', 'ارسال هزینه');
        event(new PettyCashEvent('expense.submitted', $expense));

        return $expense->refresh();
    }

    public function approveExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        if (! in_array($expense->status, [PettyCashStatuses::EXPENSE_SUBMITTED], true)) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل تأیید نیست.',
            ]);
        }

        $expense->update([
            'status' => PettyCashStatuses::EXPENSE_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->audit($expense, $actorId, 'expense_approved', 'تأیید هزینه');
        event(new PettyCashEvent('expense.approved', $expense));

        return $expense->refresh();
    }

    public function rejectExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        if (! in_array($expense->status, [PettyCashStatuses::EXPENSE_SUBMITTED, PettyCashStatuses::EXPENSE_APPROVED], true)) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل رد نیست.',
            ]);
        }

        $expense->update([
            'status' => PettyCashStatuses::EXPENSE_REJECTED,
        ]);

        $this->audit($expense, $actorId, 'expense_rejected', 'رد هزینه');
        event(new PettyCashEvent('expense.rejected', $expense));

        return $expense->refresh();
    }

    public function postExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        if ($expense->status !== PettyCashStatuses::EXPENSE_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل پرداخت نیست.',
            ]);
        }

        $expense->loadMissing('fund', 'category', 'attachments');

        if ($expense->receipt_required && $expense->attachments->isEmpty()) {
            throw ValidationException::withMessages([
                'attachments' => 'ثبت رسید برای این هزینه الزامی است.',
            ]);
        }

        $fund = $expense->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        if ($fund->current_balance < $expense->amount) {
            throw ValidationException::withMessages([
                'amount' => 'موجودی تنخواه برای پرداخت کافی نیست.',
            ]);
        }

        return DB::transaction(function () use ($expense, $fund, $actorId): PettyCashExpense {
            $journalEntry = $this->createExpenseJournal($expense, $fund);

            $fund->update([
                'current_balance' => (float) $fund->current_balance - (float) $expense->amount,
            ]);

            $expense->update([
                'status' => PettyCashStatuses::EXPENSE_PAID,
                'paid_by' => $actorId,
                'paid_at' => now(),
                'has_receipt' => $expense->attachments()->exists(),
                'accounting_journal_entry_id' => $journalEntry?->id,
            ]);

            $this->audit($expense, $actorId, 'expense_paid', 'پرداخت هزینه');
            event(new PettyCashEvent('expense.paid', $expense));

            return $expense->refresh();
        });
    }

    public function submitReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل ارسال نیست.',
            ]);
        }

        $replenishment->update([
            'status' => PettyCashStatuses::REPLENISHMENT_SUBMITTED,
            'requested_by' => $replenishment->requested_by ?: $actorId,
        ]);

        $this->audit($replenishment, $actorId, 'replenishment_submitted', 'ارسال درخواست تغذیه');
        event(new PettyCashEvent('replenishment.submitted', $replenishment));

        return $replenishment->refresh();
    }

    public function approveReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل تأیید نیست.',
            ]);
        }

        $replenishment->update([
            'status' => PettyCashStatuses::REPLENISHMENT_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->audit($replenishment, $actorId, 'replenishment_approved', 'تأیید تغذیه');
        event(new PettyCashEvent('replenishment.approved', $replenishment));

        return $replenishment->refresh();
    }

    public function rejectReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        if (! in_array($replenishment->status, [PettyCashStatuses::REPLENISHMENT_SUBMITTED, PettyCashStatuses::REPLENISHMENT_APPROVED], true)) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل رد نیست.',
            ]);
        }

        $replenishment->update([
            'status' => PettyCashStatuses::REPLENISHMENT_REJECTED,
        ]);

        $this->audit($replenishment, $actorId, 'replenishment_rejected', 'رد تغذیه');
        event(new PettyCashEvent('replenishment.rejected', $replenishment));

        return $replenishment->refresh();
    }

    public function postReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل پرداخت نیست.',
            ]);
        }

        $replenishment->loadMissing('fund');

        $fund = $replenishment->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        return DB::transaction(function () use ($replenishment, $fund, $actorId): PettyCashReplenishment {
            $journalEntry = $this->createReplenishmentJournal($replenishment, $fund);
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

            $fund->update([
                'current_balance' => (float) $fund->current_balance + (float) $replenishment->amount,
            ]);

            $replenishment->update([
                'status' => PettyCashStatuses::REPLENISHMENT_PAID,
                'paid_by' => $actorId,
                'paid_at' => now(),
                'accounting_journal_entry_id' => $journalEntry?->id,
                'accounting_treasury_transaction_id' => $treasuryTransactionId,
            ]);

            $this->audit($replenishment, $actorId, 'replenishment_paid', 'پرداخت تغذیه تنخواه');
            event(new PettyCashEvent('replenishment.paid', $replenishment));

            return $replenishment->refresh();
        });
    }

    public function submitSettlement(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل ارسال نیست.',
            ]);
        }

        $settlement->update([
            'status' => PettyCashStatuses::SETTLEMENT_SUBMITTED,
            'requested_by' => $settlement->requested_by ?: $actorId,
        ]);

        $this->audit($settlement, $actorId, 'settlement_submitted', 'ارسال تسویه');
        event(new PettyCashEvent('settlement.submitted', $settlement));

        return $settlement->refresh();
    }

    public function approveSettlement(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل تأیید نیست.',
            ]);
        }

        $settlement->update([
            'status' => PettyCashStatuses::SETTLEMENT_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->audit($settlement, $actorId, 'settlement_approved', 'تأیید تسویه');
        event(new PettyCashEvent('settlement.approved', $settlement));

        return $settlement->refresh();
    }

    public function postSettlement(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل قطعی‌سازی نیست.',
            ]);
        }

        $settlement->loadMissing('items.expense');

        return DB::transaction(function () use ($settlement, $actorId): PettyCashSettlement {
            $total = 0.0;
            foreach ($settlement->items as $item) {
                if ($item->expense && $item->expense->status === PettyCashStatuses::EXPENSE_PAID) {
                    $item->expense->update([
                        'status' => PettyCashStatuses::EXPENSE_SETTLED,
                    ]);
                    $total += (float) $item->expense->amount;
                }
            }

            $settlement->update([
                'status' => PettyCashStatuses::SETTLEMENT_POSTED,
                'posted_by' => $actorId,
                'posted_at' => now(),
                'total_expenses' => $total,
            ]);

            $this->audit($settlement, $actorId, 'settlement_posted', 'قطعی‌سازی تسویه');
            event(new PettyCashEvent('settlement.posted', $settlement));

            return $settlement->refresh();
        });
    }

    protected function createExpenseJournal(PettyCashExpense $expense, PettyCashFund $fund): ?JournalEntry
    {
        $expenseAccountId = $expense->category?->accounting_account_id ?: $fund->default_expense_account_id;
        if (! $expenseAccountId || ! $fund->accounting_cash_account_id) {
            return null;
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

        return $this->createEntry(
            $expense->company_id,
            $expense->tenant_id,
            $expense->branch_id,
            $expense->expense_date?->toDateString() ?? now()->toDateString(),
            'petty_cash_expense',
            $expense->getKey(),
            'سند تنخواه - پرداخت هزینه',
            $lines
        );
    }

    protected function createReplenishmentJournal(PettyCashReplenishment $replenishment, PettyCashFund $fund): ?JournalEntry
    {
        if (! $fund->accounting_cash_account_id || ! $fund->accounting_source_account_id) {
            return null;
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

        return $this->createEntry(
            $replenishment->company_id,
            $replenishment->tenant_id,
            $replenishment->branch_id,
            $replenishment->request_date?->toDateString() ?? now()->toDateString(),
            'petty_cash_replenishment',
            $replenishment->getKey(),
            'سند تنخواه - تغذیه',
            $lines
        );
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

    protected function audit(object $subject, ?int $actorId, string $eventType, string $description): void
    {
        $companyId = (int) ($subject->company_id ?? 0);
        if (! $companyId) {
            return;
        }

        PettyCashAuditEvent::query()->create([
            'tenant_id' => $subject->tenant_id ?? null,
            'company_id' => $companyId,
            'fund_id' => $subject->fund_id ?? null,
            'actor_id' => $actorId,
            'event_type' => $eventType,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'description' => $description,
            'metadata' => [
                'status' => $subject->status ?? null,
                'source' => 'petty_cash',
            ],
        ]);
    }
}
