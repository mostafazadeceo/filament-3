<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashFundResource\Pages\CreatePettyCashFund;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashFundResource\Pages\EditPettyCashFund;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashFundResource\Pages\ListPettyCashFunds;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class PettyCashFundResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.fund';

    protected static ?string $model = PettyCashFund::class;

    protected static ?string $modelLabel = 'تنخواه';

    protected static ?string $pluralModelLabel = 'تنخواه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(PettyCashStatuses::fundOptions())
                    ->default(PettyCashStatuses::FUND_ACTIVE),
                Select::make('custodian_user_id')
                    ->label('تنخواه‌دار')
                    ->options(fn () => \App\Models\User::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('currency')
                    ->label('واحد پول')
                    ->default('IRR')
                    ->maxLength(10),
                TextInput::make('opening_balance')
                    ->label('موجودی افتتاحیه')
                    ->numeric()
                    ->default(0),
                TextInput::make('current_balance')
                    ->label('موجودی فعلی')
                    ->numeric()
                    ->default(0)
                    ->disabled(fn (?PettyCashFund $record) => $record !== null),
                TextInput::make('threshold_balance')
                    ->label('حد آستانه')
                    ->numeric()
                    ->default(0),
                TextInput::make('replenishment_amount')
                    ->label('مبلغ پیشنهادی تغذیه')
                    ->numeric()
                    ->default(0),
                Select::make('accounting_cash_account_id')
                    ->label('حساب تنخواه (دفترکل)')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('accounting_source_account_id')
                    ->label('حساب منبع تغذیه (دفترکل)')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('default_expense_account_id')
                    ->label('حساب هزینه پیش‌فرض')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('accounting_treasury_account_id')
                    ->label('حساب خزانه')
                    ->options(fn () => TreasuryAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('branch.name')->label('شعبه'),
                TextColumn::make('current_balance')->label('موجودی'),
                TextColumn::make('threshold_balance')->label('حد آستانه'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashFunds::route('/'),
            'create' => CreatePettyCashFund::route('/create'),
            'edit' => EditPettyCashFund::route('/{record}/edit'),
        ];
    }
}
