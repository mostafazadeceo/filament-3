<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\ImpersonationSession;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Models\UserInvitation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MegaAdminOperationsWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected ?string $heading = 'عملیات و دسترسی‌ها';

    protected ?string $description = 'درخواست‌های نیازمند اقدام';

    protected int|string|array $columnSpan = 1;

    protected int|array|null $columns = ['@xl' => 2, '!@lg' => 2];

    protected function getStats(): array
    {
        $pendingAccessRequests = AccessRequest::query()
            ->where('status', 'pending')
            ->count();

        $pendingPrivilegeRequests = PrivilegeRequest::query()
            ->where('status', 'pending')
            ->count();

        $openInvitations = UserInvitation::query()
            ->whereNull('accepted_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $activeImpersonations = ImpersonationSession::query()
            ->whereNull('ended_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        return [
            Stat::make('درخواست دسترسی معلق', number_format($pendingAccessRequests))
                ->icon('heroicon-o-shield-exclamation'),
            Stat::make('درخواست نقش ممتاز', number_format($pendingPrivilegeRequests))
                ->icon('heroicon-o-key'),
            Stat::make('دعوت‌نامه‌های باز', number_format($openInvitations))
                ->icon('heroicon-o-envelope'),
            Stat::make('امپرسونیشن فعال', number_format($activeImpersonations))
                ->icon('heroicon-o-eye'),
        ];
    }
}
