<?php

namespace Haida\FilamentPettyCashIr\Http\Controllers\Api\V1;

use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Http\Resources\ExpenseResource;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AiController extends ApiController
{
    public function suggestExpense(PettyCashExpense $expense, PettyCashAiService $service): JsonResponse
    {
        $this->authorize('view', $expense);

        return response()->json($service->suggestExpense($expense));
    }

    public function applyExpenseSuggestion(PettyCashExpense $expense, PettyCashAiService $service): JsonResponse
    {
        $this->authorize('update', $expense);

        $suggestion = $service->applyExpenseSuggestion($expense, auth()->id());

        if (! $suggestion) {
            return response()->json([
                'status' => 'no_suggestion',
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'suggestion_id' => $suggestion->getKey(),
            'expense' => new ExpenseResource($expense->refresh()),
        ]);
    }

    public function rejectExpenseSuggestion(PettyCashExpense $expense, PettyCashAiService $service): JsonResponse
    {
        $this->authorize('update', $expense);

        $suggestion = $service->rejectExpenseSuggestion($expense, auth()->id());

        if (! $suggestion) {
            return response()->json([
                'status' => 'no_suggestion',
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'suggestion_id' => $suggestion->getKey(),
        ]);
    }

    public function audit(Request $request, PettyCashAiService $service): JsonResponse
    {
        $fundId = $request->integer('fund_id');
        $limit = (int) ($request->input('limit') ?? config('filament-petty-cash-ir.ai.max_scan', 200));

        $result = $service->runContinuousAudit($fundId ?: null, $limit, auth()->id());

        return response()->json($result);
    }

    public function report(Request $request, PettyCashAiService $service): JsonResponse
    {
        $fundId = $request->integer('fund_id');
        $from = $request->input('from') ? Carbon::parse((string) $request->input('from')) : null;
        $to = $request->input('to') ? Carbon::parse((string) $request->input('to')) : null;

        $report = $service->buildManagementReport($fundId ?: null, $from, $to);

        return response()->json($report);
    }
}
