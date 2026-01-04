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
use Haida\FilamentCryptoCore\Models\CryptoAccount;

class CryptoAccountResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.accounts';

    protected static ?string $model = CryptoAccount::class;

    protected static ?string $modelLabel = 'حساب رمزارز';

    protected static ?string $pluralModelLabel = 'حساب‌های رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true),
                TextInput::make('name_fa')
                    ->label('نام فارسی')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'asset' => 'دارایی',
                        'liability' => 'بدهی',
                        'revenue' => 'درآمد',
                        'expense' => 'هزینه',
                    ])
                    ->required(),
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
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable(),
                TextColumn::make('name_fa')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'asset' => 'دارایی',
                        'liability' => 'بدهی',
                        'revenue' => 'درآمد',
                        'expense' => 'هزینه',
                        default => $state,
                    }),
                TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoAccountResource\Pages\ListCryptoAccounts::route('/'),
            'create' => CryptoAccountResource\Pages\CreateCryptoAccount::route('/create'),
            'edit' => CryptoAccountResource\Pages\EditCryptoAccount::route('/{record}/edit'),
        ];
    }
}
