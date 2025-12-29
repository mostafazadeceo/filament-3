<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryAccountResource\Pages\CreateTreasuryAccount;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryAccountResource\Pages\EditTreasuryAccount;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryAccountResource\Pages\ListTreasuryAccounts;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class TreasuryAccountResource extends Resource
{
    use InteractsWithTenant;

    protected static ?string $model = TreasuryAccount::class;

    protected static ?string $modelLabel = 'حساب خزانه';

    protected static ?string $pluralModelLabel = 'حساب‌های خزانه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'حساب‌های خزانه';

    protected static string|\UnitEnum|null $navigationGroup = 'خزانه و بانک';

    protected static ?int $navigationSort = 1;

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('account_type')
                    ->label('نوع')
                    ->options([
                        'bank' => 'بانک',
                        'cash' => 'صندوق',
                        'petty_cash' => 'تنخواه',
                    ])
                    ->default('bank'),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_no')
                    ->label('شماره حساب')
                    ->maxLength(64),
                TextInput::make('iban')
                    ->label('شبا')
                    ->maxLength(64),
                TextInput::make('bank_name')
                    ->label('نام بانک')
                    ->maxLength(255),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default('IRR')
                    ->maxLength(8),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('account_type')->label('نوع')->badge(),
                TextColumn::make('bank_name')->label('بانک'),
                TextColumn::make('currency')->label('ارز'),
                ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreasuryAccounts::route('/'),
            'create' => CreateTreasuryAccount::route('/create'),
            'edit' => EditTreasuryAccount::route('/{record}/edit'),
        ];
    }
}
