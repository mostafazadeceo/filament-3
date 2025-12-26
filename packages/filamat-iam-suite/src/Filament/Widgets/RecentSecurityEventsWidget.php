<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\SecurityEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSecurityEventsWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'security.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('رویدادهای امنیتی اخیر')
            ->query(SecurityEvent::query()->latest('occurred_at'))
            ->columns([
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'auth.login' => 'ورود موفق',
                        'auth.logout' => 'خروج',
                        'auth.failed' => 'ورود ناموفق',
                        'otp.requested' => 'درخواست رمز',
                        'otp.verified' => 'تایید رمز',
                        'otp.failed' => 'خطای رمز',
                        default => $state,
                    }),
                TextColumn::make('severity')
                    ->label('شدت')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        'info' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'critical' => 'بحرانی',
                        'warning' => 'هشدار',
                        'info' => 'اطلاعات',
                        default => $state,
                    }),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('occurred_at')->label('زمان'),
            ])
            ->defaultPaginationPageOption(8)
            ->paginated([8]);
    }
}
