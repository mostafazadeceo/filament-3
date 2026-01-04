<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Ai;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;

interface AiProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function extractReceiptFields(PettyCashExpenseAttachment $attachment): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function suggestCategoryAccount(PettyCashExpense $expense, array $context = []): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function anomalyRiskScore(PettyCashExpense $expense, array $context = []): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function generatePersianDescription(PettyCashExpense $expense, array $context = []): array;
}
