<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Http\Requests\StoreSettlementRequest;
use Haida\FilamentPettyCashIr\Http\Requests\UpdateSettlementRequest;
use Haida\FilamentPettyCashIr\Http\Resources\SettlementResource;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlementItem;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettlementController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PettyCashSettlement::class, 'settlement');
    }

    public function index(): AnonymousResourceCollection
    {
        $settlements = PettyCashSettlement::query()->latest()->paginate();

        return SettlementResource::collection($settlements);
    }

    public function show(PettyCashSettlement $settlement): SettlementResource
    {
        $settlement->loadMissing('items');

        return new SettlementResource($settlement);
    }

    public function store(StoreSettlementRequest $request): SettlementResource
    {
        $data = $request->validated();
        $expenseIds = $data['expense_ids'] ?? [];
        unset($data['expense_ids']);

        $settlement = PettyCashSettlement::query()->create($data);

        if ($expenseIds) {
            $this->syncItems($settlement, $expenseIds);
        }

        return new SettlementResource($settlement->loadMissing('items'));
    }

    public function update(UpdateSettlementRequest $request, PettyCashSettlement $settlement): SettlementResource
    {
        $data = $request->validated();
        $expenseIds = $data['expense_ids'] ?? null;
        unset($data['expense_ids']);

        $settlement->update($data);

        if (is_array($expenseIds)) {
            $this->syncItems($settlement, $expenseIds, true);
        }

        return new SettlementResource($settlement->refresh()->loadMissing('items'));
    }

    public function destroy(PettyCashSettlement $settlement): array
    {
        $settlement->delete();

        return ['status' => 'ok'];
    }

    public function submit(PettyCashSettlement $settlement, PettyCashPostingService $service): SettlementResource
    {
        $this->authorize('update', $settlement);

        $settlement = $service->submitSettlement($settlement, auth()->id());

        return new SettlementResource($settlement);
    }

    public function approve(PettyCashSettlement $settlement, PettyCashPostingService $service): SettlementResource
    {
        $this->authorize('approve', $settlement);

        $settlement = $service->approveSettlement($settlement, auth()->id());

        return new SettlementResource($settlement);
    }

    public function post(PettyCashSettlement $settlement, PettyCashPostingService $service): SettlementResource
    {
        $this->authorize('post', $settlement);

        $settlement = $service->postSettlement($settlement, auth()->id());

        return new SettlementResource($settlement);
    }

    /**
     * @param  array<int, int>  $expenseIds
     */
    protected function syncItems(PettyCashSettlement $settlement, array $expenseIds, bool $replace = false): void
    {
        $expenses = PettyCashExpense::query()
            ->whereIn('id', $expenseIds)
            ->get();

        foreach ($expenses as $expense) {
            if ((int) $expense->fund_id !== (int) $settlement->fund_id) {
                throw ValidationException::withMessages([
                    'expense_ids' => 'هزینه انتخاب‌شده متعلق به تنخواه دیگری است.',
                ]);
            }
            if ($expense->status !== PettyCashStatuses::EXPENSE_PAID) {
                throw ValidationException::withMessages([
                    'expense_ids' => 'فقط هزینه‌های پرداخت‌شده قابل تسویه هستند.',
                ]);
            }
        }

        DB::transaction(function () use ($settlement, $expenseIds, $replace): void {
            if ($replace) {
                PettyCashSettlementItem::query()
                    ->where('settlement_id', $settlement->id)
                    ->delete();
            }

            foreach ($expenseIds as $expenseId) {
                PettyCashSettlementItem::query()->firstOrCreate([
                    'settlement_id' => $settlement->id,
                    'expense_id' => $expenseId,
                ], [
                    'tenant_id' => $settlement->tenant_id,
                    'company_id' => $settlement->company_id,
                ]);
            }
        });
    }
}
