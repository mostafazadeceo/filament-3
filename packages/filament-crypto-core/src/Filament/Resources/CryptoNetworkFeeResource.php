<?php

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Models\CryptoNetworkFee;

class CryptoNetworkFeeResource extends IamResource
{
    protected static ?string $permissionPrefix = 'crypto.network_fees';

    protected static ?string $model = CryptoNetworkFee::class;

    protected static ?string $modelLabel = 'کارمزد شبکه';

    protected static ?string $pluralModelLabel = 'کارمزدهای شبکه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('currency')
                    ->label('ارز')
                    ->required()
                    ->maxLength(16),
                TextInput::make('network')
                    ->label('شبکه')
                    ->required()
                    ->maxLength(32),
                TextInput::make('fee_model')
                    ->label('مدل کارمزد')
                    ->required()
                    ->maxLength(32),
                DateTimePicker::make('quoted_at')
                    ->label('زمان نرخ')
                    ->required(),
                Textarea::make('data')
                    ->label('داده (JSON)')
                    ->rows(4)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(function ($state) {
                        if (! $state) {
                            return null;
                        }

                        $decoded = json_decode((string) $state, true);

                        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('network')->label('شبکه'),
                TextColumn::make('fee_model')->label('مدل'),
                TextColumn::make('quoted_at')->label('زمان نرخ')->jalaliDateTime(),
            ])
            ->defaultSort('quoted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoNetworkFeeResource\Pages\ListCryptoNetworkFees::route('/'),
            'create' => CryptoNetworkFeeResource\Pages\CreateCryptoNetworkFee::route('/create'),
            'edit' => CryptoNetworkFeeResource\Pages\EditCryptoNetworkFee::route('/{record}/edit'),
        ];
    }
}
