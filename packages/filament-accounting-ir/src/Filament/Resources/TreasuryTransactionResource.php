<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource\Pages\CreateTreasuryTransaction;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource\Pages\EditTreasuryTransaction;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource\Pages\ListTreasuryTransactions;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;
use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;

class TreasuryTransactionResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = TreasuryTransaction::class;

    protected static ?string $modelLabel = 'تراکنش خزانه';

    protected static ?string $pluralModelLabel = 'تراکنش‌های خزانه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'تراکنش‌های خزانه';

    protected static string|\UnitEnum|null $navigationGroup = 'خزانه و بانک';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['company', 'account'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('treasury_account_id')
                    ->label('حساب خزانه')
                    ->options(fn () => TreasuryAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('transaction_type')
                    ->label('نوع')
                    ->options([
                        'deposit' => 'واریز',
                        'withdrawal' => 'برداشت',
                        'transfer' => 'انتقال',
                    ])
                    ->default('deposit'),
                DatePicker::make('transaction_date')
                    ->label('تاریخ')
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default('IRR')
                    ->maxLength(8),
                TextInput::make('reference')
                    ->label('مرجع')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('شرح')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('transaction_type')->label('نوع')->badge(),
                TextColumn::make('amount')->label('مبلغ')->numeric(decimalPlaces: 0),
                TextColumn::make('account.name')->label('حساب')->sortable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('transaction_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreasuryTransactions::route('/'),
            'create' => CreateTreasuryTransaction::route('/create'),
            'edit' => EditTreasuryTransaction::route('/{record}/edit'),
        ];
    }
}
