<?php

namespace Haida\FilamentPettyCashIr\Application\Services;

use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPettyCashIr\Infrastructure\Ai\AiProviderInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Ai\FakeAiProvider;
use Haida\FilamentPettyCashIr\Models\PettyCashAiSuggestion;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PettyCashAiService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function suggestExpense(?PettyCashExpense $expense, array $payload = []): array
    {
        if (! $this->aiEnabled() || ! $this->canUseAi()) {
            return [
                'enabled' => false,
                'message' => 'پیشنهاد هوشمند غیرفعال است یا دسترسی ندارید.',
            ];
        }

        $expense = $expense ?? PettyCashExpense::make($payload);
        $context = $this->buildExpenseContext($expense, $payload);
        $inputHash = $this->hashContext($context);

        $existing = $this->findSuggestion('expense_suggestion', $expense, $inputHash);
        if ($existing) {
            return $this->formatSuggestionResult($existing);
        }

        $provider = $this->resolveProvider();

        $categorySuggestion = $provider->suggestCategoryAccount($expense, $context);
        $descriptionSuggestion = $provider->generatePersianDescription($expense, $context);
        $riskSuggestion = $provider->anomalyRiskScore($expense, $context);

        $suggestedPayload = [
            'category_id' => $categorySuggestion['category_id'] ?? null,
            'category_hint' => $categorySuggestion['category_hint'] ?? null,
            'account_id' => $categorySuggestion['account_id'] ?? null,
            'description' => $descriptionSuggestion['description'] ?? null,
            'risk_score' => $riskSuggestion['score'] ?? null,
            'risk_label' => $riskSuggestion['label'] ?? null,
        ];

        $reasons = array_values(array_filter([
            ...($categorySuggestion['reasons'] ?? []),
            ...($descriptionSuggestion['reasons'] ?? []),
            ...($riskSuggestion['reasons'] ?? []),
        ]));

        $suggestion = PettyCashAiSuggestion::query()->create([
            'tenant_id' => $context['tenant_id'] ?? TenantContext::getTenantId(),
            'company_id' => $context['company_id'] ?? null,
            'fund_id' => $context['fund_id'] ?? null,
            'subject_type' => $expense->exists ? $expense::class : null,
            'subject_id' => $expense->exists ? $expense->getKey() : null,
            'suggestion_type' => 'expense_suggestion',
            'status' => 'proposed',
            'score' => $riskSuggestion['score'] ?? null,
            'provider' => $provider::class,
            'input_hash' => $inputHash,
            'suggested_payload' => $suggestedPayload,
            'reasons' => $reasons,
            'requested_by' => auth()->id(),
            'input_payload' => $this->shouldStorePayloads() ? $this->redactContext($context) : null,
            'output_payload' => $this->shouldStorePayloads() ? $suggestedPayload : null,
        ]);

        return $this->formatSuggestionResult($suggestion);
    }

    public function summaryForExpense(?PettyCashExpense $expense): string
    {
        if (! $expense) {
            return 'برای دریافت پیشنهاد هوشمند ابتدا هزینه را ذخیره کنید.';
        }

        $result = $this->suggestExpense($expense);
        if (! ($result['enabled'] ?? false)) {
            return (string) ($result['message'] ?? 'پیشنهاد هوشمند غیرفعال است.');
        }

        $payload = (array) ($result['suggestion'] ?? []);

        $parts = [];
        $categoryId = $payload['category_id'] ?? null;
        $categoryLabel = $categoryId ? $this->categoryLabel((int) $categoryId) : null;
        if ($categoryLabel) {
            $parts[] = 'دسته پیشنهادی: '.$categoryLabel;
        } elseif (! empty($payload['category_hint'])) {
            $parts[] = 'دسته پیشنهادی: '.$payload['category_hint'];
        }

        if (! empty($payload['description'])) {
            $parts[] = 'توضیح پیشنهادی: '.$payload['description'];
        }

        if (! empty($payload['risk_score'])) {
            $score = number_format((float) $payload['risk_score'], 2);
            $label = $payload['risk_label'] ?? null;
            $parts[] = 'ریسک: '.$score.($label ? ' ('.$label.')' : '');
        }

        if ($parts === []) {
            return 'پیشنهاد مشخصی یافت نشد.';
        }

        return implode(' | ', $parts);
    }

    public function latestExpenseSuggestion(PettyCashExpense $expense): ?PettyCashAiSuggestion
    {
        return PettyCashAiSuggestion::query()
            ->where('suggestion_type', 'expense_suggestion')
            ->where('status', 'proposed')
            ->where('subject_type', $expense::class)
            ->where('subject_id', $expense->getKey())
            ->latest('created_at')
            ->first();
    }

    public function applyExpenseSuggestion(PettyCashExpense $expense, ?int $actorId = null): ?PettyCashAiSuggestion
    {
        $suggestion = $this->latestExpenseSuggestion($expense);
        if (! $suggestion) {
            return null;
        }

        $payload = (array) ($suggestion->suggested_payload ?? []);

        $updates = [];
        if (! empty($payload['category_id'])) {
            $updates['category_id'] = (int) $payload['category_id'];
        }
        if (! empty($payload['description'])) {
            $updates['description'] = (string) $payload['description'];
        }

        if ($updates !== []) {
            $expense->update($updates);
        }

        $this->markSuggestion($suggestion, 'accepted', $actorId);

        return $suggestion->refresh();
    }

    public function rejectExpenseSuggestion(PettyCashExpense $expense, ?int $actorId = null, ?string $reason = null): ?PettyCashAiSuggestion
    {
        $suggestion = $this->latestExpenseSuggestion($expense);
        if (! $suggestion) {
            return null;
        }

        $this->markSuggestion($suggestion, 'rejected', $actorId, $reason);

        return $suggestion->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    public function extractReceiptFields(PettyCashExpenseAttachment $attachment, ?int $actorId = null): array
    {
        if (! $this->aiEnabled() || ! $this->canUseAi()) {
            return [
                'enabled' => false,
                'message' => 'AI غیرفعال است یا دسترسی ندارید.',
            ];
        }

        $context = [
            'tenant_id' => $attachment->tenant_id ?? TenantContext::getTenantId(),
            'company_id' => $attachment->company_id ?? null,
            'fund_id' => $attachment->expense?->fund_id,
            'attachment_id' => $attachment->getKey(),
            'path' => $attachment->path,
            'mime_type' => $attachment->mime_type,
        ];
        $inputHash = $this->hashContext($context);

        $existing = $this->findSuggestion('receipt_fields', $attachment, $inputHash);
        if ($existing) {
            return $this->formatSuggestionResult($existing);
        }

        $provider = $this->resolveProvider();
        $result = $provider->extractReceiptFields($attachment);

        $suggestion = PettyCashAiSuggestion::query()->create([
            'tenant_id' => $context['tenant_id'] ?? TenantContext::getTenantId(),
            'company_id' => $context['company_id'] ?? null,
            'fund_id' => $context['fund_id'] ?? null,
            'subject_type' => $attachment::class,
            'subject_id' => $attachment->getKey(),
            'suggestion_type' => 'receipt_fields',
            'status' => 'proposed',
            'provider' => $provider::class,
            'input_hash' => $inputHash,
            'suggested_payload' => $result,
            'reasons' => $result['reasons'] ?? null,
            'requested_by' => $actorId ?? auth()->id(),
            'input_payload' => $this->shouldStorePayloads() ? $this->redactContext($context) : null,
            'output_payload' => $this->shouldStorePayloads() ? $result : null,
        ]);

        $attachment->update([
            'metadata' => array_merge((array) $attachment->metadata, [
                'receipt_fields' => $result,
            ]),
        ]);

        return $this->formatSuggestionResult($suggestion);
    }

    public function analyzeExpense(PettyCashExpense $expense, ?int $actorId = null): ?PettyCashAiSuggestion
    {
        if (! $this->aiEnabled() || ! $this->canUseAi()) {
            return null;
        }

        $context = $this->buildExpenseContext($expense, []);
        $context['analysis_type'] = 'anomaly';
        $inputHash = $this->hashContext($context);

        $existing = $this->findSuggestion('anomaly_risk', $expense, $inputHash);
        if ($existing) {
            return $existing;
        }

        $provider = $this->resolveProvider();
        $risk = $provider->anomalyRiskScore($expense, $context);

        $score = (float) ($risk['score'] ?? 0);
        $threshold = (float) config('filament-petty-cash-ir.ai.anomaly_threshold', 0.7);
        if ($score < $threshold) {
            return null;
        }

        $suggestion = PettyCashAiSuggestion::query()->create([
            'tenant_id' => $context['tenant_id'] ?? TenantContext::getTenantId(),
            'company_id' => $context['company_id'] ?? null,
            'fund_id' => $context['fund_id'] ?? null,
            'subject_type' => $expense::class,
            'subject_id' => $expense->getKey(),
            'suggestion_type' => 'anomaly_risk',
            'status' => 'proposed',
            'score' => $score,
            'provider' => $provider::class,
            'input_hash' => $inputHash,
            'suggested_payload' => [
                'risk_score' => $score,
                'risk_label' => $risk['label'] ?? null,
            ],
            'reasons' => $risk['reasons'] ?? null,
            'requested_by' => $actorId ?? auth()->id(),
            'input_payload' => $this->shouldStorePayloads() ? $this->redactContext($context) : null,
            'output_payload' => $this->shouldStorePayloads() ? $risk : null,
        ]);

        if ((bool) config('filament-petty-cash-ir.ai.create_exceptions', true)) {
            app(PettyCashControlService::class)->recordException(
                'ai_anomaly',
                'ریسک غیرعادی هزینه',
                $score >= 0.8 ? 'high' : 'medium',
                $expense,
                [
                    'score' => $score,
                    'label' => $risk['label'] ?? null,
                    'description' => 'ریسک غیرعادی بر اساس تحلیل هوشمند شناسایی شد.',
                    'reasons' => $risk['reasons'] ?? [],
                ],
                $expense->fund_id,
                $expense->company_id,
                $actorId
            );
        }

        return $suggestion;
    }

    /**
     * @return array<string, mixed>
     */
    public function runContinuousAudit(?int $fundId = null, int $limit = 200, ?int $actorId = null): array
    {
        if (! $this->aiEnabled() || ! $this->canUseAi()) {
            return [
                'enabled' => false,
                'message' => 'AI غیرفعال است یا دسترسی ندارید.',
            ];
        }

        $query = PettyCashExpense::query()
            ->whereIn('status', [
                PettyCashStatuses::EXPENSE_SUBMITTED,
                PettyCashStatuses::EXPENSE_APPROVED,
                PettyCashStatuses::EXPENSE_PAID,
            ])
            ->orderByDesc('expense_date');

        if ($fundId) {
            $query->where('fund_id', $fundId);
        }

        $expenses = $query->limit($limit)->get();

        $flagged = [];

        foreach ($expenses as $expense) {
            $suggestion = $this->analyzeExpense($expense, $actorId);
            if ($suggestion instanceof PettyCashAiSuggestion) {
                $flagged[] = $suggestion;
            }
        }

        return [
            'enabled' => true,
            'checked' => $expenses->count(),
            'flagged' => count($flagged),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildManagementReport(?int $fundId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ? $from->copy()->startOfDay() : now()->subDays(30)->startOfDay();
        $to = $to ? $to->copy()->endOfDay() : now()->endOfDay();

        $query = PettyCashExpense::query()->whereBetween('expense_date', [$from, $to]);
        if ($fundId) {
            $query->where('fund_id', $fundId);
        }

        $totalAmount = (float) $query->sum('amount');
        $totalCount = (int) $query->count();

        $openStatuses = [
            PettyCashStatuses::EXPENSE_SUBMITTED,
            PettyCashStatuses::EXPENSE_APPROVED,
        ];

        $amountAtRisk = (float) PettyCashExpense::query()
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->whereIn('status', $openStatuses)
            ->sum('amount');

        $closed = PettyCashExpense::query()
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->whereNotNull('paid_at')
            ->whereBetween('expense_date', [$from, $to])
            ->get(['expense_date', 'paid_at']);

        $avgCloseDays = $this->averageCloseDays($closed);

        $exceptionsOpen = PettyCashControlException::query()
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->where('status', '!=', 'resolved')
            ->count();

        $anomalyCount = PettyCashAiSuggestion::query()
            ->where('suggestion_type', 'anomaly_risk')
            ->where('status', 'proposed')
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->count();

        $previousFrom = $from->copy()->subDays($from->diffInDays($to) + 1);
        $previousTo = $from->copy()->subDay();

        $previousTotal = (float) PettyCashExpense::query()
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->whereBetween('expense_date', [$previousFrom, $previousTo])
            ->sum('amount');

        $previousCount = (int) PettyCashExpense::query()
            ->when($fundId, fn ($builder) => $builder->where('fund_id', $fundId))
            ->whereBetween('expense_date', [$previousFrom, $previousTo])
            ->count();

        $amountDelta = $totalAmount - $previousTotal;
        $countDelta = $totalCount - $previousCount;
        $amountDeltaPercent = $previousTotal > 0 ? ($amountDelta / $previousTotal) * 100 : null;

        return [
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'totals' => [
                'count' => $totalCount,
                'amount' => $totalAmount,
                'amount_delta' => $amountDelta,
                'amount_delta_percent' => $amountDeltaPercent,
                'count_delta' => $countDelta,
            ],
            'controls' => [
                'amount_at_risk' => $amountAtRisk,
                'exceptions_open' => $exceptionsOpen,
                'ai_anomalies' => $anomalyCount,
                'avg_close_days' => $avgCloseDays,
            ],
        ];
    }

    public function aiEnabled(): bool
    {
        return (bool) config('filament-petty-cash-ir.ai.enabled', false);
    }

    public function canUseAi(): bool
    {
        return IamAuthorization::allows('petty_cash.ai.use');
    }

    protected function resolveProvider(): AiProviderInterface
    {
        $provider = config('filament-petty-cash-ir.ai.provider', FakeAiProvider::class);

        return app($provider);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function buildExpenseContext(PettyCashExpense $expense, array $payload): array
    {
        return [
            'tenant_id' => $expense->tenant_id ?? TenantContext::getTenantId(),
            'company_id' => $payload['company_id'] ?? $expense->company_id ?? null,
            'fund_id' => $payload['fund_id'] ?? $expense->fund_id ?? null,
            'category_id' => $payload['category_id'] ?? $expense->category_id ?? null,
            'amount' => (float) ($payload['amount'] ?? $expense->amount ?? 0),
            'currency' => $payload['currency'] ?? $expense->currency ?? null,
            'expense_date' => $payload['expense_date'] ?? $expense->expense_date?->toDateString(),
            'description' => $payload['description'] ?? $expense->description ?? null,
            'reference' => $payload['reference'] ?? $expense->reference ?? null,
            'payee_name' => $payload['payee_name'] ?? $expense->payee_name ?? null,
            'status' => $payload['status'] ?? $expense->status ?? null,
            'receipt_required' => $payload['receipt_required'] ?? $expense->receipt_required ?? null,
            'has_receipt' => $payload['has_receipt'] ?? $expense->has_receipt ?? null,
        ];
    }

    protected function hashContext(array $context): string
    {
        return hash('sha256', json_encode($context));
    }

    protected function shouldStorePayloads(): bool
    {
        if (! (bool) config('filament-petty-cash-ir.ai.allow_store_prompts', false)) {
            return false;
        }

        return IamAuthorization::allows('petty_cash.ai.manage_settings');
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function redactContext(array $context): array
    {
        $redactions = (array) config('filament-petty-cash-ir.ai.redaction', []);
        foreach ($redactions as $key) {
            if (array_key_exists($key, $context)) {
                $context[$key] = '[REDACTED]';
            }
        }

        return $context;
    }

    protected function findSuggestion(string $type, ?Model $subject, string $inputHash): ?PettyCashAiSuggestion
    {
        $query = PettyCashAiSuggestion::query()
            ->where('suggestion_type', $type)
            ->where('input_hash', $inputHash)
            ->where('status', 'proposed');

        if ($subject && $subject->getKey()) {
            $query->where('subject_type', $subject::class)
                ->where('subject_id', $subject->getKey());
        } else {
            $query->whereNull('subject_type')
                ->whereNull('subject_id');
        }

        return $query->latest('created_at')->first();
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatSuggestionResult(PettyCashAiSuggestion $suggestion): array
    {
        return [
            'enabled' => true,
            'suggestion_id' => $suggestion->getKey(),
            'suggestion' => (array) ($suggestion->suggested_payload ?? []),
            'reasons' => (array) ($suggestion->reasons ?? []),
            'score' => $suggestion->score,
        ];
    }

    protected function markSuggestion(PettyCashAiSuggestion $suggestion, string $status, ?int $actorId, ?string $reason = null): void
    {
        $metadata = (array) ($suggestion->metadata ?? []);
        if ($reason) {
            $metadata['decision_reason'] = $reason;
        }

        $suggestion->update([
            'status' => $status,
            'decided_by' => $actorId,
            'decided_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    protected function categoryLabel(int $categoryId): ?string
    {
        return PettyCashCategory::query()->whereKey($categoryId)->value('name');
    }

    /**
     * @param  Collection<int, PettyCashExpense>  $expenses
     */
    protected function averageCloseDays(Collection $expenses): ?float
    {
        if ($expenses->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($expenses as $expense) {
            if (! $expense->paid_at || ! $expense->expense_date) {
                continue;
            }

            $totalDays += $expense->paid_at->diffInDays($expense->expense_date);
            $count++;
        }

        if ($count === 0) {
            return null;
        }

        return round($totalDays / $count, 2);
    }
}
