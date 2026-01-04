<?php

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Models\CryptoWallet;

class CryptoWalletResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.wallets';

    protected static ?string $model = CryptoWallet::class;

    protected static ?string $modelLabel = 'کیف‌پول رمزارز';

    protected static ?string $pluralModelLabel = 'کیف‌پول‌های رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('mode')
                    ->label('نوع')
                    ->options([
                        'custodial' => 'امانی',
                        'watch_only' => 'ناظر',
                        'provider' => 'درگاهی',
                    ])
                    ->required(),
                TextInput::make('provider')
                    ->label('درگاه')
                    ->maxLength(64)
                    ->nullable(),
                TextInput::make('label')
                    ->label('برچسب')
                    ->required()
                    ->maxLength(255),
                TextInput::make('currency')
                    ->label('ارز')
                    ->required()
                    ->maxLength(16),
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
                    ->default('active'),
                Textarea::make('meta')
                    ->label('متا (JSON)')
                    ->rows(3)
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
                TextColumn::make('label')->label('برچسب')->searchable(),
                TextColumn::make('mode')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'custodial' => 'امانی',
                        'watch_only' => 'ناظر',
                        'provider' => 'درگاهی',
                        default => $state,
                    }),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('network')->label('شبکه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => $state === 'active' ? 'فعال' : 'غیرفعال'),
                TextColumn::make('addresses_count')->label('آدرس‌ها')->counts('addresses'),
                TextColumn::make('updated_at')->label('به‌روزرسانی')->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoWalletResource\Pages\ListCryptoWallets::route('/'),
            'create' => CryptoWalletResource\Pages\CreateCryptoWallet::route('/create'),
            'edit' => CryptoWalletResource\Pages\EditCryptoWallet::route('/{record}/edit'),
        ];
    }
}
