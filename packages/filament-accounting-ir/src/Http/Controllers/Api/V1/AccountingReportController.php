<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\GeneralLedgerReportRequest;
use Vendor\FilamentAccountingIr\Http\Requests\TrialBalanceReportRequest;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\JournalLine;

class AccountingReportController extends Controller
{
    public function trialBalance(TrialBalanceReportRequest $request): array
    {
        $data = $request->validated();
        $companyId = (int) $data['company_id'];
        $fiscalYearId = $data['fiscal_year_id'] ?? null;
        $branchId = $data['branch_id'] ?? null;
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;

        $rows = JournalLine::query()
            ->select([
                'accounting_ir_journal_lines.account_id',
                'accounts.code as account_code',
                'accounts.name as account_name',
                DB::raw('SUM(accounting_ir_journal_lines.debit) as total_debit'),
                DB::raw('SUM(accounting_ir_journal_lines.credit) as total_credit'),
            ])
            ->join('accounting_ir_journal_entries as entries', 'entries.id', '=', 'accounting_ir_journal_lines.journal_entry_id')
            ->join('accounting_ir_chart_accounts as accounts', 'accounts.id', '=', 'accounting_ir_journal_lines.account_id')
            ->where('accounting_ir_journal_lines.company_id', $companyId)
            ->where('entries.status', 'posted')
            ->when($fiscalYearId, fn ($query) => $query->where('entries.fiscal_year_id', $fiscalYearId))
            ->when($branchId, fn ($query) => $query->where('entries.branch_id', $branchId))
            ->when($from, fn ($query) => $query->whereDate('entries.entry_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('entries.entry_date', '<=', $to))
            ->groupBy('accounting_ir_journal_lines.account_id', 'accounts.code', 'accounts.name')
            ->orderBy('accounts.code')
            ->get()
            ->map(function ($row): array {
                $debit = (float) $row->total_debit;
                $credit = (float) $row->total_credit;

                return [
                    'account_id' => (int) $row->account_id,
                    'code' => $row->account_code,
                    'name' => $row->account_name,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $debit - $credit,
                ];
            })
            ->values();

        $totals = [
            'debit' => (float) $rows->sum('debit'),
            'credit' => (float) $rows->sum('credit'),
        ];
        $totals['balance'] = $totals['debit'] - $totals['credit'];

        return [
            'data' => $rows,
            'totals' => $totals,
        ];
    }

    public function generalLedger(GeneralLedgerReportRequest $request): array
    {
        $data = $request->validated();
        $companyId = (int) $data['company_id'];
        $accountId = (int) $data['account_id'];
        $fiscalYearId = $data['fiscal_year_id'] ?? null;
        $branchId = $data['branch_id'] ?? null;
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;

        $account = ChartAccount::query()
            ->where('company_id', $companyId)
            ->whereKey($accountId)
            ->firstOrFail();

        $baseQuery = JournalLine::query()
            ->select([
                'accounting_ir_journal_lines.id',
                'accounting_ir_journal_lines.journal_entry_id',
                'accounting_ir_journal_lines.description',
                'accounting_ir_journal_lines.debit',
                'accounting_ir_journal_lines.credit',
                'accounting_ir_journal_lines.currency',
                'entries.entry_no',
                'entries.entry_date',
                'entries.description as entry_description',
                'entries.branch_id',
            ])
            ->join('accounting_ir_journal_entries as entries', 'entries.id', '=', 'accounting_ir_journal_lines.journal_entry_id')
            ->where('accounting_ir_journal_lines.company_id', $companyId)
            ->where('accounting_ir_journal_lines.account_id', $accountId)
            ->where('entries.status', 'posted')
            ->when($fiscalYearId, fn ($query) => $query->where('entries.fiscal_year_id', $fiscalYearId))
            ->when($branchId, fn ($query) => $query->where('entries.branch_id', $branchId));

        $openingBalance = 0.0;
        if ($from) {
            $opening = (clone $baseQuery)
                ->whereDate('entries.entry_date', '<', $from)
                ->select([
                    DB::raw('SUM(accounting_ir_journal_lines.debit) as total_debit'),
                    DB::raw('SUM(accounting_ir_journal_lines.credit) as total_credit'),
                ])
                ->first();

            $openingBalance = ((float) ($opening->total_debit ?? 0)) - ((float) ($opening->total_credit ?? 0));
        }

        $lines = (clone $baseQuery)
            ->when($from, fn ($query) => $query->whereDate('entries.entry_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('entries.entry_date', '<=', $to))
            ->orderBy('entries.entry_date')
            ->orderBy('entries.id')
            ->orderBy('accounting_ir_journal_lines.id')
            ->get();

        $running = $openingBalance;
        $rows = $lines->map(function ($line) use (&$running): array {
            $debit = (float) $line->debit;
            $credit = (float) $line->credit;
            $running += $debit - $credit;
            $entryDate = $line->entry_date;
            if ($entryDate instanceof \Carbon\CarbonInterface) {
                $entryDate = $entryDate->toDateString();
            }

            return [
                'line_id' => (int) $line->id,
                'entry_id' => (int) $line->journal_entry_id,
                'entry_no' => $line->entry_no,
                'entry_date' => $entryDate,
                'branch_id' => $line->branch_id,
                'description' => $line->description,
                'entry_description' => $line->entry_description,
                'debit' => $debit,
                'credit' => $credit,
                'currency' => $line->currency,
                'balance' => $running,
            ];
        });

        $totals = [
            'debit' => (float) $rows->sum('debit'),
            'credit' => (float) $rows->sum('credit'),
        ];
        $totals['balance'] = $openingBalance + $totals['debit'] - $totals['credit'];

        return [
            'account' => [
                'id' => $account->getKey(),
                'code' => $account->code,
                'name' => $account->name,
            ],
            'opening_balance' => $openingBalance,
            'lines' => $rows,
            'totals' => $totals,
        ];
    }
}
