<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource\Pages\CreateCryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource\Pages\EditCryptoPayoutDestination;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutDestinationResource\Pages\ListCryptoPayoutDestinations;
use Haida\FilamentCryptoGateway\Models\CryptoPayoutDestination;

class CryptoPayoutDestinationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.payout_destinations';

    protected static ?string $model = CryptoPayoutDestination::class;

    protected static ?string $modelLabel = 'مقصد برداشت';

    protected static ?string $pluralModelLabel = 'لیست سفید برداشت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('label')
                    ->label('برچسب')
                    ->maxLength(120)
                    ->nullable(),
                TextInput::make('address')
                    ->label('آدرس')
                    ->required()
                    ->maxLength(255),
                TextInput::make('currency')
                    ->label('ارز')
                    ->maxLength(16)
                    ->nullable(),
                TextInput::make('network')
                    ->label('شبکه')
                    ->maxLength(32)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('meta')
                    ->label('متا (JSON)')
                    ->rows(4)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('label')
                    ->label('برچسب')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('آدرس')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('currency')
                    ->label('ارز')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('network')
                    ->label('شبکه')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('approved_at')
                    ->label('تایید')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_used_at')
                    ->label('آخرین استفاده')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoPayoutDestinations::route('/'),
            'create' => CreateCryptoPayoutDestination::route('/create'),
            'edit' => EditCryptoPayoutDestination::route('/{record}/edit'),
        ];
    }
}
