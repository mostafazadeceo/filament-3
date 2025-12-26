<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\SecurityEventResource\Pages\ListSecurityEvents;
use Filamat\IamSuite\Models\SecurityEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SecurityEventResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'security';

    protected static ?string $model = SecurityEvent::class;

    protected static ?string $navigationLabel = 'رویدادهای امنیتی';

    protected static ?string $pluralModelLabel = 'رویدادهای امنیتی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('severity')
                    ->label('شدت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'info' => 'اطلاع',
                        'warning' => 'هشدار',
                        'critical' => 'بحرانی',
                        default => $state,
                    }),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('ip')->label('آی‌پی'),
                TextColumn::make('occurred_at')->label('زمان'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecurityEvents::route('/'),
        ];
    }
}
