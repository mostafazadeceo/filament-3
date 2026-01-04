<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Ai;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;

class FakeAiProvider implements AiProviderInterface
{
    public function extractReceiptFields(PettyCashExpenseAttachment $attachment): array
    {
        $metadata = (array) ($attachment->metadata ?? []);

        return [
            'vendor' => $metadata['vendor'] ?? null,
            'date' => $metadata['date'] ?? null,
            'amount' => $metadata['amount'] ?? null,
            'currency' => $metadata['currency'] ?? 'IRR',
            'confidence' => 0.3,
            'reasons' => ['استخراج آزمایشی بر اساس متادیتا انجام شد.'],
        ];
    }

    public function suggestCategoryAccount(PettyCashExpense $expense, array $context = []): array
    {
        $description = (string) ($expense->description ?? '');
        $hint = $description !== '' ? 'بر اساس توضیحات موجود' : 'الگوی عمومی';

        return [
            'category_id' => $expense->category_id,
            'account_id' => null,
            'category_hint' => $hint,
            'confidence' => 0.35,
            'reasons' => [
                'ارائه‌دهنده آزمایشی دسته‌بندی قطعی ارائه نمی‌کند.',
                $hint,
            ],
        ];
    }

    public function anomalyRiskScore(PettyCashExpense $expense, array $context = []): array
    {
        $amount = (float) ($expense->amount ?? 0);

        $score = match (true) {
            $amount >= 5_000_000 => 0.85,
            $amount >= 1_000_000 => 0.55,
            default => 0.2,
        };

        $label = $score >= 0.75 ? 'high' : ($score >= 0.5 ? 'medium' : 'low');

        return [
            'score' => $score,
            'label' => $label,
            'reasons' => [
                $amount >= 1_000_000 ? 'مبلغ نسبتاً بالا ثبت شده است.' : 'الگوی مبلغ معمولی است.',
                'ارزیابی آزمایشی است و نیاز به بررسی انسانی دارد.',
            ],
        ];
    }

    public function generatePersianDescription(PettyCashExpense $expense, array $context = []): array
    {
        $amount = (float) ($expense->amount ?? 0);
        $currency = (string) ($expense->currency ?? 'IRR');
        $base = $expense->description ?: 'هزینه تنخواه';
        $description = $amount > 0
            ? $base.' به مبلغ '.number_format($amount, 0).' '.$currency
            : $base;

        return [
            'description' => $description,
            'confidence' => 0.4,
            'reasons' => [
                'متن پیشنهادی آزمایشی بر اساس داده‌های فعلی تولید شد.',
            ],
        ];
    }
}
