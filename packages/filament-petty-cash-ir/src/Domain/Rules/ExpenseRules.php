<?php

namespace Haida\FilamentPettyCashIr\Domain\Rules;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Validation\ValidationException;

class ExpenseRules
{
    public function assertCanSubmit(PettyCashExpense $expense): void
    {
        if ($expense->status !== PettyCashStatuses::EXPENSE_DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل ارسال نیست.',
            ]);
        }
    }

    public function assertCanApprove(PettyCashExpense $expense): void
    {
        if ($expense->status !== PettyCashStatuses::EXPENSE_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل تأیید نیست.',
            ]);
        }
    }

    public function assertCanReject(PettyCashExpense $expense): void
    {
        if (! in_array($expense->status, [PettyCashStatuses::EXPENSE_SUBMITTED, PettyCashStatuses::EXPENSE_APPROVED], true)) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل رد نیست.',
            ]);
        }
    }

    public function assertCanPost(PettyCashExpense $expense): void
    {
        if ($expense->status !== PettyCashStatuses::EXPENSE_APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل پرداخت نیست.',
            ]);
        }
    }

    public function assertCanReverse(PettyCashExpense $expense): void
    {
        if (! in_array($expense->status, [PettyCashStatuses::EXPENSE_PAID], true)) {
            throw ValidationException::withMessages([
                'status' => 'هزینه در وضعیت قابل برگشت نیست.',
            ]);
        }
    }

    public function assertReceiptSatisfied(PettyCashExpense $expense): void
    {
        if ($expense->receipt_required && ! $expense->attachments()->exists()) {
            throw ValidationException::withMessages([
                'attachments' => 'ثبت رسید برای این هزینه الزامی است.',
            ]);
        }
    }

    public function assertFundBalance(PettyCashFund $fund, float $amount): void
    {
        if ((float) $fund->current_balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => 'موجودی تنخواه برای پرداخت کافی نیست.',
            ]);
        }
    }
}
