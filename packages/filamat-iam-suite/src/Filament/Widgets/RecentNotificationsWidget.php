<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentNotificationsWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('اعلان‌های اخیر')
            ->query(Notification::query()->latest('created_at'))
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sent' => 'success',
                        'queued' => 'warning',
                        'failed' => 'danger',
                        'skipped' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'queued' => 'در صف',
                        'sent' => 'ارسال شده',
                        'failed' => 'ناموفق',
                        'skipped' => 'رد شده',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('زمان'),
            ])
            ->defaultPaginationPageOption(8)
            ->paginated([8]);
    }
}
