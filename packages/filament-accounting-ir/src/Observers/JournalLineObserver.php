<?php

namespace Vendor\FilamentAccountingIr\Observers;

use Vendor\FilamentAccountingIr\Models\JournalLine;

class JournalLineObserver
{
    public function saved(JournalLine $line): void
    {
        $this->syncEntryTotals($line);
    }

    public function deleted(JournalLine $line): void
    {
        $this->syncEntryTotals($line);
    }

    protected function syncEntryTotals(JournalLine $line): void
    {
        $entry = $line->entry;
        if (! $entry) {
            return;
        }

        $totals = $entry->lines()
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $entry->update([
            'total_debit' => $totals->total_debit ?? 0,
            'total_credit' => $totals->total_credit ?? 0,
        ]);
    }
}
