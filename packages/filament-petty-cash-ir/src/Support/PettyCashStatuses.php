<?php

namespace Haida\FilamentPettyCashIr\Support;

final class PettyCashStatuses
{
    public const FUND_ACTIVE = 'active';

    public const FUND_INACTIVE = 'inactive';

    public const EXPENSE_DRAFT = 'draft';

    public const EXPENSE_SUBMITTED = 'submitted';

    public const EXPENSE_APPROVED = 'approved';

    public const EXPENSE_PAID = 'paid';

    public const EXPENSE_REJECTED = 'rejected';

    public const EXPENSE_SETTLED = 'settled';

    public const REPLENISHMENT_DRAFT = 'draft';

    public const REPLENISHMENT_SUBMITTED = 'submitted';

    public const REPLENISHMENT_APPROVED = 'approved';

    public const REPLENISHMENT_PAID = 'paid';

    public const REPLENISHMENT_REJECTED = 'rejected';

    public const SETTLEMENT_DRAFT = 'draft';

    public const SETTLEMENT_SUBMITTED = 'submitted';

    public const SETTLEMENT_APPROVED = 'approved';

    public const SETTLEMENT_POSTED = 'posted';

    /**
     * @return array<string, string>
     */
    public static function fundOptions(): array
    {
        return [
            self::FUND_ACTIVE => 'فعال',
            self::FUND_INACTIVE => 'غیرفعال',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function expenseOptions(): array
    {
        return [
            self::EXPENSE_DRAFT => 'پیش‌نویس',
            self::EXPENSE_SUBMITTED => 'ارسال‌شده',
            self::EXPENSE_APPROVED => 'تأیید‌شده',
            self::EXPENSE_PAID => 'پرداخت‌شده',
            self::EXPENSE_REJECTED => 'رد‌شده',
            self::EXPENSE_SETTLED => 'تسویه‌شده',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function replenishmentOptions(): array
    {
        return [
            self::REPLENISHMENT_DRAFT => 'پیش‌نویس',
            self::REPLENISHMENT_SUBMITTED => 'ارسال‌شده',
            self::REPLENISHMENT_APPROVED => 'تأیید‌شده',
            self::REPLENISHMENT_PAID => 'پرداخت‌شده',
            self::REPLENISHMENT_REJECTED => 'رد‌شده',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function settlementOptions(): array
    {
        return [
            self::SETTLEMENT_DRAFT => 'پیش‌نویس',
            self::SETTLEMENT_SUBMITTED => 'ارسال‌شده',
            self::SETTLEMENT_APPROVED => 'تأیید‌شده',
            self::SETTLEMENT_POSTED => 'قطعی‌شده',
        ];
    }
}
