<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class NotificationFailuresWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('ناموفق‌های اخیر')
            ->query(Notification::query()
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subDays(30))
                ->latest('created_at'))
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('channel')->label('کانال'),
                TextColumn::make('payload.error')
                    ->label('خطا')
                    ->limit(50)
                    ->formatStateUsing(fn ($state) => $state ?: '—'),
                TextColumn::make('created_at')->label('زمان'),
            ])
            ->defaultPaginationPageOption(8)
            ->paginated([8]);
    }
}
