<?php

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Models\CryptoAddress;
use Haida\FilamentCryptoCore\Models\CryptoWallet;

class CryptoAddressResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.addresses';

    protected static ?string $model = CryptoAddress::class;

    protected static ?string $modelLabel = 'آدرس رمزارز';

    protected static ?string $pluralModelLabel = 'آدرس‌های رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('wallet_id')
                    ->label('کیف‌پول')
                    ->options(fn () => CryptoWallet::query()->pluck('label', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('address')
                    ->label('آدرس')
                    ->required(),
                TextInput::make('tag_memo')
                    ->label('تگ/ممو')
                    ->nullable(),
                TextInput::make('derivation_path')
                    ->label('مسیر مشتق')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'blocked' => 'مسدود',
                    ])
                    ->default('active'),
                DateTimePicker::make('last_seen_at')
                    ->label('آخرین مشاهده')
                    ->nullable(),
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
                TextColumn::make('wallet.label')->label('کیف‌پول')->searchable(),
                TextColumn::make('address')->label('آدرس')->searchable(),
                TextColumn::make('tag_memo')->label('تگ/ممو'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'blocked' => 'مسدود',
                        default => $state,
                    }),
                TextColumn::make('last_seen_at')->label('آخرین مشاهده')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('به‌روزرسانی')->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoAddressResource\Pages\ListCryptoAddresses::route('/'),
            'create' => CryptoAddressResource\Pages\CreateCryptoAddress::route('/create'),
            'edit' => CryptoAddressResource\Pages\EditCryptoAddress::route('/{record}/edit'),
        ];
    }
}
