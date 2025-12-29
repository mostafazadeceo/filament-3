<?php

namespace Haida\FilamentPettyCashIr\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentPettyCashIr\Policies\PettyCashCategoryPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashExpensePolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashFundPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashReplenishmentPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashSettlementPolicy;

final class PettyCashCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-petty-cash-ir',
            self::permissions(),
            [
                'petty_cash' => true,
            ],
            [],
            [
                PettyCashFundPolicy::class,
                PettyCashCategoryPolicy::class,
                PettyCashExpensePolicy::class,
                PettyCashReplenishmentPolicy::class,
                PettyCashSettlementPolicy::class,
            ],
            [
                'petty_cash' => 'تنخواه',
                'petty_cash_master' => 'اطلاعات پایه',
                'petty_cash_ops' => 'عملیات',
                'petty_cash_report' => 'گزارش‌ها',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'petty_cash.view',
            'petty_cash.fund.view',
            'petty_cash.fund.manage',
            'petty_cash.category.view',
            'petty_cash.category.manage',
            'petty_cash.expense.view',
            'petty_cash.expense.manage',
            'petty_cash.expense.approve',
            'petty_cash.expense.post',
            'petty_cash.expense.reject',
            'petty_cash.expense.settle',
            'petty_cash.replenishment.view',
            'petty_cash.replenishment.manage',
            'petty_cash.replenishment.approve',
            'petty_cash.replenishment.post',
            'petty_cash.replenishment.reject',
            'petty_cash.settlement.view',
            'petty_cash.settlement.manage',
            'petty_cash.settlement.approve',
            'petty_cash.settlement.post',
            'petty_cash.report.view',
            'petty_cash.report.export',
        ];
    }
}
