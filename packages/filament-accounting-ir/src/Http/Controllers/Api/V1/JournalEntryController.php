<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreJournalEntryRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateJournalEntryRequest;
use Vendor\FilamentAccountingIr\Http\Resources\JournalEntryResource;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Services\JournalEntryService;

class JournalEntryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $entries = JournalEntry::query()
            ->with('lines')
            ->latest('entry_date')
            ->paginate();

        return JournalEntryResource::collection($entries);
    }

    public function show(JournalEntry $journalEntry): JournalEntryResource
    {
        $journalEntry->load('lines');

        return new JournalEntryResource($journalEntry);
    }

    public function store(StoreJournalEntryRequest $request): JournalEntryResource
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $entry = DB::transaction(function () use ($data, $lines): JournalEntry {
            $entry = JournalEntry::query()->create($data);
            $this->syncLines($entry, $lines);

            return $entry->refresh();
        });

        return new JournalEntryResource($entry->load('lines'));
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry): JournalEntryResource
    {
        if ($journalEntry->status === 'posted') {
            throw ValidationException::withMessages([
                'status' => 'سند قطعی قابل ویرایش نیست.',
            ]);
        }

        $data = $request->validated();
        $lines = $data['lines'] ?? null;
        unset($data['lines']);

        $entry = DB::transaction(function () use ($journalEntry, $data, $lines): JournalEntry {
            $journalEntry->update($data);
            if (is_array($lines)) {
                $this->syncLines($journalEntry, $lines);
            }

            return $journalEntry->refresh();
        });

        return new JournalEntryResource($entry->load('lines'));
    }

    public function submit(JournalEntry $journalEntry): JournalEntryResource
    {
        $entry = app(JournalEntryService::class)->submit($journalEntry);

        return new JournalEntryResource($entry);
    }

    public function approve(JournalEntry $journalEntry): JournalEntryResource
    {
        $entry = app(JournalEntryService::class)->approve($journalEntry);

        return new JournalEntryResource($entry);
    }

    public function post(JournalEntry $journalEntry): JournalEntryResource
    {
        $entry = app(JournalEntryService::class)->post($journalEntry);

        return new JournalEntryResource($entry);
    }

    public function reverse(Request $request, JournalEntry $journalEntry): JournalEntryResource
    {
        $entryNo = $request->string('entry_no')->toString();
        if ($entryNo === '') {
            $entryNo = $journalEntry->entry_no.'-R';
        }

        $reversal = app(JournalEntryService::class)->reverse($journalEntry, $entryNo);

        return new JournalEntryResource($reversal->load('lines'));
    }

    protected function syncLines(JournalEntry $entry, array $lines): void
    {
        $entry->lines()->delete();

        foreach ($lines as $line) {
            if (($line['debit'] ?? 0) <= 0 && ($line['credit'] ?? 0) <= 0) {
                continue;
            }

            $entry->lines()->create([
                'tenant_id' => $entry->tenant_id,
                'company_id' => $entry->company_id,
                'account_id' => $line['account_id'],
                'description' => $line['description'] ?? null,
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
                'currency' => $line['currency'] ?? 'IRR',
                'amount' => $line['amount'] ?? null,
                'exchange_rate' => $line['exchange_rate'] ?? null,
                'dimensions' => $line['dimensions'] ?? null,
            ]);
        }

        $this->refreshTotals($entry);
        app(JournalEntryService::class)->validateEntry($entry);
    }

    protected function refreshTotals(JournalEntry $entry): void
    {
        $totals = $entry->lines()
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(credit),0) as total_credit')
            ->first();

        $entry->update([
            'total_debit' => $totals->total_debit ?? 0,
            'total_credit' => $totals->total_credit ?? 0,
        ]);
    }

    //
}
