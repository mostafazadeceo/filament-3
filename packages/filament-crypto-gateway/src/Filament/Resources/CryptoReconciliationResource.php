<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoReconciliationResource\Pages\ListCryptoReconciliations;
use Haida\FilamentCryptoGateway\Models\CryptoReconciliation;

class CryptoReconciliationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.reconciliations';

    protected static ?string $model = CryptoReconciliation::class;

    protected static ?string $modelLabel = 'آشتی سازی';

    protected static ?string $pluralModelLabel = 'آشتی سازی‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('scope')
                    ->label('دامنه')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('started_at')
                    ->label('شروع')
                    ->jalaliDateTime(),
                TextColumn::make('ended_at')
                    ->label('پایان')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDateTime(),
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
            'index' => ListCryptoReconciliations::route('/'),
        ];
    }
}
