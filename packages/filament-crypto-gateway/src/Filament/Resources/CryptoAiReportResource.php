<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoAiReportResource\Pages\ListCryptoAiReports;
use Haida\FilamentCryptoGateway\Models\CryptoAiReport;

class CryptoAiReportResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.ai_reports';

    protected static ?string $model = CryptoAiReport::class;

    protected static ?string $modelLabel = 'گزارش هوشمند';

    protected static ?string $pluralModelLabel = 'گزارش‌های هوشمند';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('period')
                    ->label('دوره')
                    ->badge(),
                TextColumn::make('meta.provider')
                    ->label('درگاه')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('report_at')
                    ->label('زمان گزارش')
                    ->jalaliDateTime(),
                TextColumn::make('summary_md')
                    ->label('خلاصه')
                    ->limit(60),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoAiReports::route('/'),
        ];
    }
}
