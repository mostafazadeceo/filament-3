<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Enums\CryptoProviderEnvironment;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource\Pages\CreateCryptoProviderAccount;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource\Pages\EditCryptoProviderAccount;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoProviderAccountResource\Pages\ListCryptoProviderAccounts;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;

class CryptoProviderAccountResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.providers';

    protected static ?string $model = CryptoProviderAccount::class;

    protected static ?string $modelLabel = 'اتصال درگاه';

    protected static ?string $pluralModelLabel = 'اتصالات درگاه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('provider')
                    ->label('ارائه دهنده')
                    ->required()
                    ->maxLength(100),
                Select::make('env')
                    ->label('محیط')
                    ->options([
                        CryptoProviderEnvironment::Sandbox->value => 'تست',
                        CryptoProviderEnvironment::Production->value => 'تولید',
                    ])
                    ->default(CryptoProviderEnvironment::Sandbox->value)
                    ->required(),
                TextInput::make('merchant_id')
                    ->label('شناسه پذیرنده')
                    ->maxLength(150)
                    ->nullable(),
                TextInput::make('api_key_encrypted')
                    ->label('کلید API')
                    ->password()
                    ->maxLength(200)
                    ->nullable(),
                TextInput::make('secret_encrypted')
                    ->label('کلید مخفی')
                    ->password()
                    ->maxLength(200)
                    ->nullable(),
                Textarea::make('config_json')
                    ->label('تنظیمات (JSON)')
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
                Select::make('is_active')
                    ->label('فعال')
                    ->options([
                        true => 'بله',
                        false => 'خیر',
                    ])
                    ->default(true)
                    ->required(),
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
                TextColumn::make('provider')
                    ->label('درگاه')
                    ->searchable(),
                TextColumn::make('env')
                    ->label('محیط')
                    ->badge(),
                TextColumn::make('merchant_id')
                    ->label('شناسه پذیرنده')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoProviderAccounts::route('/'),
            'create' => CreateCryptoProviderAccount::route('/create'),
            'edit' => EditCryptoProviderAccount::route('/{record}/edit'),
        ];
    }
}
