<?php

namespace Haida\FilamentPettyCashIr\Domain\Rules;

use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Validation\ValidationException;

class SettlementRules
{
    public function assertCanSubmit(PettyCashSettlement $settlement): void
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل ارسال نیست.',
            ]);
        }
    }

    public function assertCanApprove(PettyCashSettlement $settlement): void
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل تأیید نیست.',
            ]);
        }
    }

    public function assertCanPost(PettyCashSettlement $settlement): void
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل قطعی‌سازی نیست.',
            ]);
        }
    }

    public function assertCanReverse(PettyCashSettlement $settlement): void
    {
        if ($settlement->status !== PettyCashStatuses::SETTLEMENT_POSTED) {
            throw ValidationException::withMessages([
                'status' => 'تسویه در وضعیت قابل برگشت نیست.',
            ]);
        }
    }
}
