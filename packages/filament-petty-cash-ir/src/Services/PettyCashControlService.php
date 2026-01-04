<?php

namespace Haida\FilamentPettyCashIr\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Illuminate\Database\Eloquent\Model;

class PettyCashControlService
{
    public function recordException(
        string $ruleCode,
        string $title,
        string $severity,
        ?Model $subject = null,
        array $metadata = [],
        ?int $fundId = null,
        ?int $companyId = null,
        ?int $detectedBy = null
    ): PettyCashControlException {
        $tenantId = $subject?->tenant_id ?? TenantContext::getTenantId();
        $companyId = $subject?->company_id ?? $companyId;

        if (! $companyId) {
            return PettyCashControlException::query()->make();
        }

        return PettyCashControlException::query()->updateOrCreate([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'rule_code' => $ruleCode,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'status' => 'open',
        ], [
            'fund_id' => $fundId ?? ($subject->fund_id ?? null),
            'severity' => $severity,
            'title' => $title,
            'description' => $metadata['description'] ?? null,
            'detected_at' => now(),
            'detected_by' => $detectedBy,
            'metadata' => $metadata,
        ]);
    }

    public function checkFundThreshold(PettyCashFund $fund): ?PettyCashControlException
    {
        $threshold = (float) ($fund->threshold_balance ?? 0);
        if ($threshold <= 0) {
            return null;
        }

        if ((float) $fund->current_balance >= $threshold) {
            return null;
        }

        return $this->recordException(
            'low_balance',
            'کاهش موجودی تنخواه',
            'high',
            $fund,
            [
                'threshold_balance' => $threshold,
                'current_balance' => (float) $fund->current_balance,
                'description' => 'موجودی تنخواه کمتر از حد آستانه است.',
            ],
            $fund->getKey()
        );
    }

    public function checkDuplicateReceipt(PettyCashExpenseAttachment $attachment): ?PettyCashControlException
    {
        if (! $attachment->content_hash) {
            return null;
        }

        $duplicateExists = PettyCashExpenseAttachment::query()
            ->where('tenant_id', $attachment->tenant_id)
            ->where('company_id', $attachment->company_id)
            ->where('content_hash', $attachment->content_hash)
            ->where('id', '!=', $attachment->id)
            ->exists();

        if (! $duplicateExists) {
            return null;
        }

        $expense = $attachment->expense;
        $subject = $expense instanceof PettyCashExpense ? $expense : $attachment;

        return $this->recordException(
            'duplicate_receipt',
            'رسید تکراری',
            'medium',
            $subject,
            [
                'attachment_id' => $attachment->id,
                'content_hash' => $attachment->content_hash,
                'description' => 'رسید مشابه برای این هزینه یا هزینه دیگری ثبت شده است.',
            ],
            $expense?->fund_id
        );
    }
}
