<?php

namespace Haida\FilamentPettyCashIr\Filament\Widgets;

use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class PettyCashOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'خلاصه تنخواه';

    public static function canView(): bool
    {
        return IamAuthorization::allows('petty_cash.report.view')
            || IamAuthorization::allows('petty_cash.exceptions.view');
    }

    protected function getStats(): array
    {
        $tenantId = TenantContext::getTenantId();

        $fundQuery = PettyCashFund::query();
        $expenseQuery = PettyCashExpense::query();
        $exceptionQuery = PettyCashControlException::query();

        if ($tenantId) {
            $fundQuery->where('tenant_id', $tenantId);
            $expenseQuery->where('tenant_id', $tenantId);
            $exceptionQuery->where('tenant_id', $tenantId);
        }

        $fundCount = $fundQuery->count();
        $fundBalance = (float) $fundQuery->sum('current_balance');

        $recentExpenses = $expenseQuery
            ->whereIn('status', [PettyCashStatuses::EXPENSE_PAID, PettyCashStatuses::EXPENSE_SETTLED])
            ->whereDate('expense_date', '>=', now()->subDays(30)->toDateString())
            ->get(['amount']);

        $expenseTotal = (float) $recentExpenses->sum('amount');
        $expenseCount = $recentExpenses->count();

        $openExceptions = $exceptionQuery->where('status', 'open')->count();

        return [
            Stat::make('تنخواه‌های فعال', (string) $fundCount)
                ->description('جمع موجودی: '.number_format($fundBalance)),
            Stat::make('هزینه‌های ۳۰ روز اخیر', number_format($expenseTotal))
                ->description('تعداد: '.number_format($expenseCount)),
            Stat::make('استثناهای باز', number_format($openExceptions))
                ->description('کنترل‌های نیازمند پیگیری'),
        ];
    }
}
