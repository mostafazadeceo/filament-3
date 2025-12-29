<?php

namespace Vendor\FilamentAccountingIr\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Events\JournalEntryPosted;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\JournalEntry;

class JournalEntryService
{
    public function submit(JournalEntry $entry): JournalEntry
    {
        $this->ensureEditable($entry);

        $entry->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $entry->refresh();
    }

    public function approve(JournalEntry $entry): JournalEntry
    {
        $this->ensureEditable($entry);

        $entry->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return $entry->refresh();
    }

    public function post(JournalEntry $entry): JournalEntry
    {
        $this->ensureEditable($entry);
        $this->validateEntry($entry);

        $entry->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);

        event(new JournalEntryPosted($entry));

        return $entry->refresh();
    }

    public function reverse(JournalEntry $entry, string $entryNo): JournalEntry
    {
        return DB::transaction(function () use ($entry, $entryNo): JournalEntry {
            $entry->load('lines');

            $reversal = JournalEntry::query()->create([
                'tenant_id' => $entry->tenant_id,
                'company_id' => $entry->company_id,
                'branch_id' => $entry->branch_id,
                'fiscal_year_id' => $entry->fiscal_year_id,
                'fiscal_period_id' => $entry->fiscal_period_id,
                'entry_no' => $entryNo,
                'entry_date' => now(),
                'status' => 'draft',
                'description' => 'سند معکوس برای: '.$entry->entry_no,
                'reversed_entry_id' => $entry->id,
            ]);

            foreach ($entry->lines as $line) {
                $reversal->lines()->create([
                    'tenant_id' => $line->tenant_id,
                    'company_id' => $line->company_id,
                    'account_id' => $line->account_id,
                    'description' => $line->description,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'currency' => $line->currency,
                    'amount' => $line->amount,
                    'exchange_rate' => $line->exchange_rate,
                    'dimensions' => $line->dimensions,
                ]);
            }

            $totals = $reversal->lines()
                ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
                ->first();

            $reversal->update([
                'total_debit' => $totals->total_debit ?? 0,
                'total_credit' => $totals->total_credit ?? 0,
            ]);

            return $reversal->refresh();
        });
    }

    public function validateEntry(JournalEntry $entry): void
    {
        $entry->loadMissing('lines', 'fiscalYear', 'fiscalPeriod');

        if ($entry->lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => 'حداقل یک ردیف برای سند لازم است.',
            ]);
        }

        $fiscalYear = $entry->fiscalYear;
        if ($fiscalYear instanceof FiscalYear && $fiscalYear->is_closed) {
            throw ValidationException::withMessages([
                'fiscal_year_id' => 'سال مالی بسته شده است.',
            ]);
        }

        $fiscalPeriod = $entry->fiscalPeriod;
        if ($fiscalPeriod instanceof FiscalPeriod && $fiscalPeriod->is_closed) {
            throw ValidationException::withMessages([
                'fiscal_period_id' => 'دوره مالی بسته شده است.',
            ]);
        }

        if ($fiscalYear instanceof FiscalYear) {
            $entryDate = $entry->entry_date?->toDateString();
            if ($entryDate < $fiscalYear->start_date->toDateString() || $entryDate > $fiscalYear->end_date->toDateString()) {
                throw ValidationException::withMessages([
                    'entry_date' => 'تاریخ سند خارج از سال مالی است.',
                ]);
            }
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($entry->lines as $line) {
            $debit = (float) $line->debit;
            $credit = (float) $line->credit;

            if ($debit > 0 && $credit > 0) {
                throw ValidationException::withMessages([
                    'lines' => 'هر ردیف نمی‌تواند هم بدهکار و هم بستانکار باشد.',
                ]);
            }

            if ($debit <= 0 && $credit <= 0) {
                throw ValidationException::withMessages([
                    'lines' => 'هر ردیف باید بدهکار یا بستانکار باشد.',
                ]);
            }

            $account = $line->account ?? ChartAccount::query()->find($line->account_id);
            if ($account && ! $account->is_postable) {
                throw ValidationException::withMessages([
                    'lines' => 'ثبت سند روی حساب غیرقابل ثبت مجاز نیست.',
                ]);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (round($totalDebit - $totalCredit, 2) !== 0.0) {
            throw ValidationException::withMessages([
                'lines' => 'تراز بدهکار و بستانکار برابر نیست.',
            ]);
        }
    }

    protected function ensureEditable(JournalEntry $entry): void
    {
        if ($entry->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'سند قطعی قابل تغییر نیست.',
            ]);
        }
    }
}
