<?php

namespace Haida\FilamentPettyCashIr\Domain\Rules;

use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Validation\ValidationException;

class ReplenishmentRules
{
    public function assertCanSubmit(PettyCashReplenishment $replenishment): void
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل ارسال نیست.',
            ]);
        }
    }

    public function assertCanApprove(PettyCashReplenishment $replenishment): void
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل تأیید نیست.',
            ]);
        }
    }

    public function assertCanReject(PettyCashReplenishment $replenishment): void
    {
        if (! in_array($replenishment->status, [PettyCashStatuses::REPLENISHMENT_SUBMITTED, PettyCashStatuses::REPLENISHMENT_APPROVED], true)) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل رد نیست.',
            ]);
        }
    }

    public function assertCanPost(PettyCashReplenishment $replenishment): void
    {
        if ($replenishment->status !== PettyCashStatuses::REPLENISHMENT_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل پرداخت نیست.',
            ]);
        }
    }

    public function assertCanReverse(PettyCashReplenishment $replenishment): void
    {
        if (! in_array($replenishment->status, [PettyCashStatuses::REPLENISHMENT_PAID], true)) {
            throw ValidationException::withMessages([
                'status' => 'تغذیه در وضعیت قابل برگشت نیست.',
            ]);
        }
    }

    public function assertFundBalanceForReversal(PettyCashFund $fund, float $amount): void
    {
        if ((float) $fund->current_balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => 'موجودی تنخواه برای برگشت تغذیه کافی نیست.',
            ]);
        }
    }
}
