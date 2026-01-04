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
use Haida\FilamentCryptoCore\Models\CryptoFeePolicy;

class CryptoFeePolicyResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.fee_policies';

    protected static ?string $model = CryptoFeePolicy::class;

    protected static ?string $modelLabel = 'سیاست کارمزد';

    protected static ?string $pluralModelLabel = 'سیاست‌های کارمزد';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('plan_key')
                    ->label('کلید پلن')
                    ->required()
                    ->maxLength(32),
                TextInput::make('invoice_percent')
                    ->label('درصد کارمزد فاکتور')
                    ->numeric()
                    ->required(),
                TextInput::make('invoice_fixed')
                    ->label('کارمزد ثابت فاکتور')
                    ->numeric()
                    ->required(),
                TextInput::make('payout_fixed')
                    ->label('کارمزد برداشت')
                    ->numeric()
                    ->required(),
                TextInput::make('conversion_percent')
                    ->label('درصد تبدیل')
                    ->numeric()
                    ->required(),
                Select::make('network_fee_mode')
                    ->label('مدل کارمزد شبکه')
                    ->options([
                        'pass_through' => 'عبور مستقیم',
                        'absorb' => 'جذب توسط پلتفرم',
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
                TextColumn::make('plan_key')->label('پلن'),
                TextColumn::make('invoice_percent')->label('درصد فاکتور'),
                TextColumn::make('invoice_fixed')->label('ثابت فاکتور'),
                TextColumn::make('payout_fixed')->label('برداشت'),
                TextColumn::make('network_fee_mode')
                    ->label('مدل شبکه')
                    ->formatStateUsing(fn (string $state) => $state === 'absorb' ? 'جذب' : 'عبور مستقیم'),
                TextColumn::make('updated_at')->label('به‌روزرسانی')->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => CryptoFeePolicyResource\Pages\ListCryptoFeePolicies::route('/'),
            'create' => CryptoFeePolicyResource\Pages\CreateCryptoFeePolicy::route('/create'),
            'edit' => CryptoFeePolicyResource\Pages\EditCryptoFeePolicy::route('/{record}/edit'),
        ];
    }
}
