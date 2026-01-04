<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamAiReportResource\Pages\ListIamAiReports;
use Filamat\IamSuite\Models\IamAiReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IamAiReportResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'automation.reports';

    protected static ?string $model = IamAiReport::class;

    protected static ?string $navigationLabel = 'گزارش‌های هوش';

    protected static ?string $pluralModelLabel = 'گزارش‌های هوش';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'اتوماسیون';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('title')->label('عنوان')->limit(40),
                TextColumn::make('severity')
                    ->label('شدت')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'critical' => 'بحرانی',
                        'high' => 'بالا',
                        'medium' => 'متوسط',
                        'low' => 'کم',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'new' => 'جدید',
                        'received' => 'دریافت شد',
                        'reviewed' => 'بررسی شد',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('زمان'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIamAiReports::route('/'),
        ];
    }
}
