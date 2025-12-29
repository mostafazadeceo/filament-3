<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Http\Requests\StoreExpenseRequest;
use Haida\FilamentPettyCashIr\Http\Requests\UpdateExpenseRequest;
use Haida\FilamentPettyCashIr\Http\Resources\ExpenseResource;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PettyCashExpense::class, 'expense');
    }

    public function index(): AnonymousResourceCollection
    {
        $expenses = PettyCashExpense::query()->latest()->paginate();

        return ExpenseResource::collection($expenses);
    }

    public function show(PettyCashExpense $expense): ExpenseResource
    {
        $expense->loadMissing('attachments');

        return new ExpenseResource($expense);
    }

    public function store(StoreExpenseRequest $request): ExpenseResource
    {
        $expense = PettyCashExpense::query()->create($request->validated());

        return new ExpenseResource($expense);
    }

    public function update(UpdateExpenseRequest $request, PettyCashExpense $expense): ExpenseResource
    {
        $expense->update($request->validated());

        return new ExpenseResource($expense->refresh());
    }

    public function destroy(PettyCashExpense $expense): array
    {
        $expense->delete();

        return ['status' => 'ok'];
    }

    public function submit(PettyCashExpense $expense, PettyCashPostingService $service): ExpenseResource
    {
        $this->authorize('update', $expense);

        $expense = $service->submitExpense($expense, auth()->id());

        return new ExpenseResource($expense);
    }

    public function approve(PettyCashExpense $expense, PettyCashPostingService $service): ExpenseResource
    {
        $this->authorize('approve', $expense);

        $expense = $service->approveExpense($expense, auth()->id());

        return new ExpenseResource($expense);
    }

    public function reject(PettyCashExpense $expense, PettyCashPostingService $service): ExpenseResource
    {
        $this->authorize('reject', $expense);

        $expense = $service->rejectExpense($expense, auth()->id());

        return new ExpenseResource($expense);
    }

    public function post(PettyCashExpense $expense, PettyCashPostingService $service): ExpenseResource
    {
        $this->authorize('post', $expense);

        $expense = $service->postExpense($expense, auth()->id());

        return new ExpenseResource($expense);
    }
}
