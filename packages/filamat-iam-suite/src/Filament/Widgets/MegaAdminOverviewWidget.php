<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MegaAdminOverviewWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected ?string $heading = 'نمای کلی پلتفرم';

    protected ?string $description = 'نمایش وضعیت کلان و رشد اخیر';

    protected function getStats(): array
    {
        $userModel = config('auth.providers.users.model');

        $orgCount = Organization::query()->count();
        $tenantCount = Tenant::query()->count();
        $activeTenants = Tenant::query()->where('status', 'active')->count();
        $inactiveTenants = max(0, $tenantCount - $activeTenants);

        $userCount = $userModel::query()->count();
        $newUsers = $userModel::query()->where('created_at', '>=', now()->subDays(7))->count();
        $newTenants = Tenant::query()->where('created_at', '>=', now()->subDays(7))->count();

        $activeSubscriptions = Subscription::query()->where('status', 'active')->count();
        $mrr = (float) Subscription::query()
            ->where('status', 'active')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price');

        return [
            Stat::make('سازمان‌ها', number_format($orgCount))
                ->description('کل سازمان‌ها')
                ->icon('heroicon-o-building-office'),
            Stat::make('فضاهای کاری', number_format($tenantCount))
                ->description('فعال: '.number_format($activeTenants).' | غیرفعال: '.number_format($inactiveTenants))
                ->icon('heroicon-o-rectangle-group'),
            Stat::make('فضاهای کاری جدید (۷ روز)', number_format($newTenants))
                ->icon('heroicon-o-sparkles'),
            Stat::make('کاربران', number_format($userCount))
                ->description('جدید ۷ روز: '.number_format($newUsers))
                ->icon('heroicon-o-users'),
            Stat::make('اشتراک‌های فعال', number_format($activeSubscriptions))
                ->icon('heroicon-o-ticket'),
            Stat::make('MRR تقریبی', number_format($mrr, 2))
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
