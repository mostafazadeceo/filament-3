<?php

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Models\CryptoRate;

class CryptoRateResource extends IamResource
{
    protected static ?string $permissionPrefix = 'crypto.rates';

    protected static ?string $model = CryptoRate::class;

    protected static ?string $modelLabel = 'نرخ رمزارز';

    protected static ?string $pluralModelLabel = 'نرخ‌های رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('from')
                    ->label('از')
                    ->required()
                    ->maxLength(16),
                TextInput::make('to')
                    ->label('به')
                    ->required()
                    ->maxLength(16),
                TextInput::make('rate')
                    ->label('نرخ')
                    ->numeric()
                    ->required(),
                TextInput::make('source')
                    ->label('منبع')
                    ->nullable(),
                DateTimePicker::make('quoted_at')
                    ->label('زمان نرخ')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from')->label('از'),
                TextColumn::make('to')->label('به'),
                TextColumn::make('rate')->label('نرخ'),
                TextColumn::make('source')->label('منبع'),
                TextColumn::make('quoted_at')->label('زمان نرخ')->jalaliDateTime(),
            ])
            ->defaultSort('quoted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoRateResource\Pages\ListCryptoRates::route('/'),
            'create' => CryptoRateResource\Pages\CreateCryptoRate::route('/create'),
            'edit' => CryptoRateResource\Pages\EditCryptoRate::route('/{record}/edit'),
        ];
    }
}
